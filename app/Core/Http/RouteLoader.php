<?php

declare(strict_types=1);

namespace App\Core\Http;

use ReflectionClass;
use ReflectionMethod;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use App\Core\Http\Helpers\Stack;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Attributes\RouteAttribute;
use App\Core\Http\Attributes\ControllerAttr;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Attributes\Middleware as MiddlewareAttr;
use App\Core\Http\Attributes\NamePrefix;
use App\Core\Exception\LoaderAttributeException;
use App\Core\Http\Helpers\ClassControllers;
use App\Core\Http\Helpers\RouteCollection;
use App\Core\Http\Helpers\RouteDefinition;

/**
 * RouteLoader — Scansiona i controller, legge gli attributi di routing
 * e costruisce la RouteCollection.
 *
 * Supporta:
 * - Legacy: #[RouteAttr] + #[ControllerAttr]
 * - Spatie-style: #[Get], #[Post], #[Put], #[Patch], #[Delete] + #[Prefix], #[Middleware], #[NamePrefix]
 */
class RouteLoader
{
    /** @var array<string, string> */
    private array $controllersPath;

    /**
     * @param array<string, string> $controllerPath [namespace => directory path]
     */
    public function __construct(array $controllerPath)
    {
        $this->controllersPath = $controllerPath;
    }

    /**
     * Carica tutte le rotte dai controller registrati.
     */
    public function load(): RouteCollection
    {
        // 1. Ritorna tutti i controller con namespace e file
        $controllers = $this->getAllControllers();

        // 2. Filtra classi astratte e interfacce
        $classControllers = $this->getReflectedControllers($controllers);

        // 3. Prepara lo stack (middleware/basePath) per ogni controller
        $controllerStacks = $this->getControllerStacks($classControllers);

        // 4. Estrai le rotte
        return $this->extractRoutes($controllerStacks);
    }

    /**
     * Scansiona ricorsivamente le directory dei controller e ritorna
     * un array [FQCN => filepath].
     *
     * @return array<string, string>
     */
    private function getAllControllers(): array
    {
        $controllers = [];

        foreach ($this->controllersPath as $namespace => $path) {
            if (!is_dir($path)) {
                throw new LoaderAttributeException(
                    "Controller directory not found: '{$path}' for namespace '{$namespace}'. "
                    . "Check your config/controllers.php configuration."
                );
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relative = str_replace($path . DIRECTORY_SEPARATOR, '', $file->getPathname());

                    $className = $namespace . '\\' . str_replace(
                        [DIRECTORY_SEPARATOR, '.php'],
                        ['\\', ''],
                        $relative
                    );

                    $controllers[$className] = $file->getPathname();
                }
            }
        }

        return $controllers;
    }

    /**
     * Filtra le classi controller: esclude astratte e non istanziabili.
     *
     * @param array<string, string> $controllers
     */
    private function getReflectedControllers(array $controllers): ClassControllers
    {
        $reflected = new ClassControllers();

        foreach ($controllers as $className => $fileController) {
            if (!class_exists($className)) {
                require_once $fileController;
            }

            if (!class_exists($className)) {
                throw new LoaderAttributeException(
                    "Class '{$className}' not found after requiring '{$fileController}'. "
                    . "Verify that the namespace matches the file path."
                );
            }

            $reflection = new ReflectionClass($className);

            if ($reflection->isAbstract() || !$reflection->isInstantiable()) {
                continue;
            }

            $reflected->addController($className);
        }

        return $reflected;
    }

    /**
     * Per ogni controller, costruisce lo Stack di middleware e basePath
     * risalendo la gerarchia delle classi.
     */
    public function getControllerStacks(ClassControllers $classControllers): ClassControllers
    {
        foreach ($classControllers as $className => $stack) {
            $reflection = new ReflectionClass($className);
            $controllerStack = $this->buildControllerStack($reflection);
            $classControllers->setStack(className: $className, stack: $controllerStack);
        }

        return $classControllers;
    }

    /**
     * Costruisce lo Stack combinando gli attributi legacy (#[ControllerAttr])
     * e i nuovi attributi Spatie-style (#[Prefix], #[Middleware], #[NamePrefix]).
     */
    private function buildControllerStack(ReflectionClass $rc): Stack
    {
        $stack = new Stack();

        // Risali la gerarchia: prima le superclassi, ordine coerente
        $chain = [];
        $cursor = $rc;
        while ($cursor) {
            $chain[] = $cursor;
            $cursor = $cursor->getParentClass();
        }
        $chain = array_reverse($chain);

        foreach ($chain as $classRef) {
            // --- Legacy: #[ControllerAttr] ---
            foreach ($classRef->getAttributes(ControllerAttr::class) as $controllerAttribute) {
                try {
                    $instance = $controllerAttribute->newInstance();

                    if (property_exists($instance, 'middlewareNames') && !empty($instance->middlewareNames)) {
                        $stack->addMiddleware((array) $instance->middlewareNames);
                    }

                    if (property_exists($instance, 'basePath') && !empty($instance->basePath)) {
                        $stack->addPath($instance->basePath);
                    }
                } catch (LoaderAttributeException $e) {
                    throw new LoaderAttributeException(
                        "Invalid #[ControllerAttr] in {$classRef->getName()} ({$classRef->getFileName()}): "
                        . "expected constructor parameters are missing or malformed. "
                        . "Check: #[ControllerAttr(middlewareNames, basePath, routeName)]"
                    );
                }
            }

            // --- Spatie-style: #[Prefix] ---
            foreach ($classRef->getAttributes(Prefix::class) as $attr) {
                try {
                    $instance = $attr->newInstance();
                    $stack->addPath($instance->prefix);
                } catch (\Throwable $e) {
                    throw new LoaderAttributeException(
                        "Invalid #[Prefix] in {$classRef->getName()} ({$classRef->getFileName()}): "
                        . "expected a string path. Example: #[Prefix('/admin')]"
                    );
                }
            }

            // --- Spatie-style: #[Middleware] ---
            foreach ($classRef->getAttributes(MiddlewareAttr::class) as $attr) {
                try {
                    $instance = $attr->newInstance();
                    $stack->addMiddleware($instance->middleware);
                } catch (\Throwable $e) {
                    throw new LoaderAttributeException(
                        "Invalid #[Middleware] in {$classRef->getName()} ({$classRef->getFileName()}): "
                        . "expected an array of middleware names. Example: #[Middleware(['auth'])]"
                    );
                }
            }

            // --- Spatie-style: #[NamePrefix] ---
            foreach ($classRef->getAttributes(NamePrefix::class) as $attr) {
                try {
                    $instance = $attr->newInstance();
                    $stack->setNamePrefix($instance->namePrefix);
                } catch (\Throwable $e) {
                    throw new LoaderAttributeException(
                        "Invalid #[NamePrefix] in {$classRef->getName()} ({$classRef->getFileName()}): "
                        . "expected a string prefix. Example: #[NamePrefix('admin.')]"
                    );
                }
            }
        }

        $stack->clean();

        return $stack;
    }

    /**
     * Estrai le rotte da tutti i controller.
     * Supporta sia #[RouteAttr] (legacy) sia i nuovi attributi Spatie-style.
     */
    private function extractRoutes(ClassControllers $controllers): RouteCollection
    {
        $routes = new RouteCollection();

        foreach ($controllers->all() as $className => $stack) {
            $reflection = new ReflectionClass($className);

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->isConstructor()) {
                    continue;
                }
                if ($method->class !== $reflection->getName()) {
                    continue;
                }

                // Raccogli attributi di routing (legacy + nuovi)
                $routeInstances = $this->collectRouteAttributes($method);

                // Se il metodo pubblico non ha attributi di routing, salta silenziosamente.
                // Questo permette metodi helper pubblici nei controller.
                if (empty($routeInstances)) {
                    continue;
                }

                foreach ($routeInstances as $route) {
                    // Componi il path completo: basePath ereditato + path del metodo
                    $basePath = implode('', $stack->Path()->toArray());
                    $fullPath = rtrim($basePath, '/') . $route->path;
                    $fullPath = preg_replace('#/+#', '/', $fullPath);

                    // Merge middleware: controller + rotta
                    $middlewares = array_merge(
                        $stack->Middleware()->toArray(),
                        $route->middleware ?? []
                    );

                    // Componi il nome: namePrefix della classe + name della rotta
                    $routeName = $route->name;
                    $namePrefix = $stack->getNamePrefix();
                    if ($routeName !== null && $namePrefix !== '') {
                        $routeName = $namePrefix . $routeName;
                    }

                    $routes->add(new RouteDefinition(
                        $fullPath,
                        strtoupper($route->method),
                        $className,
                        $method->getName(),
                        $routeName,
                        array_unique($middlewares)
                    ));
                }
            }
        }

        return $routes;
    }

    /**
     * Raccogli tutti gli attributi di routing da un metodo.
     * Supporta sia RouteAttr (legacy) sia RouteAttribute (nuovo) e le sue sottoclassi.
     *
     * @return array<RouteAttr|RouteAttribute>
     */
    private function collectRouteAttributes(ReflectionMethod $method): array
    {
        $instances = [];

        // Legacy: #[RouteAttr]
        foreach ($method->getAttributes(RouteAttr::class) as $attr) {
            try {
                $instances[] = $attr->newInstance();
            } catch (\Throwable $e) {
                throw new LoaderAttributeException(
                    "Invalid #[RouteAttr] on {$method->class}::{$method->getName()}(): "
                    . $e->getMessage() . ". "
                    . "Expected: #[RouteAttr('/path', 'METHOD', 'name', ['middleware'])]"
                );
            }
        }

        // Spatie-style: #[Get], #[Post], #[Put], #[Patch], #[Delete] (tutti estendono RouteAttribute)
        foreach ($method->getAttributes(RouteAttribute::class, \ReflectionAttribute::IS_INSTANCEOF) as $attr) {
            try {
                $instances[] = $attr->newInstance();
            } catch (\Throwable $e) {
                throw new LoaderAttributeException(
                    "Invalid route attribute on {$method->class}::{$method->getName()}(): "
                    . $e->getMessage() . ". "
                    . "Expected: #[Get('/path', name: 'name', middleware: ['mw'])]"
                );
            }
        }

        return $instances;
    }
}
