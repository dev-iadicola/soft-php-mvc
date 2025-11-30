<?php

namespace App\Core\Http;

use App\Core\Http\Helpers\DynamicRoute;
use ReflectionClass;
use ReflectionMethod;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use App\Core\Http\Helpers\Stack;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Attributes\ControllerAttr;
use App\Core\Exception\LoaderAttributeException;
use App\Core\Http\Helpers\ClassControllers;
use App\Core\Http\Helpers\RouteCollection;
use App\Core\Http\Helpers\RouteDefinition;

/**
 * Summary of RouterLoader
 * 
 * - Scansiona i controller del progetto
 * - Legge gli attributi #[RouteAttr] e #[ControllerAttr]
 * - Costruisci un array di rotte (path, method HTTP, action, ecc.)
 */
class RouteLoader
{
    private array $controllersPath;
    /**
     * Summary of __construct
     * necessita di un array con [namespace => percorso del controller]
     * @param array<string,string> $conntrollerPath
     */
    public function __construct(
        array $conntrollerPath
    ) {
        $this->controllersPath = $conntrollerPath;
    }



    /**
     * 2#
     * Summary of getReflrectedController
     * Filtriamo tutte le classi controller eccetto astratte e interfacce
     * @param array $controllers
     * @return Stack
     */
    private function getReflrectedController(array $controllers): ClassControllers
    {
        $reflected = new ClassControllers();
        foreach ($controllers as $className => $fileController) {

            if (!class_exists($className)) {
                require_once $fileController;
            }

            $refleciton = new ReflectionClass($className);

            /**
             * * Al momento igoriamo classi astratte e interfacce
             * 
             * Le riprenderemo nel metodo collectInheritedClassAttributes
             *
             * @return array
             */
            if ($refleciton->isAbstract() || !$refleciton->isInstantiable())
                continue;

            $reflected->addController($className);
        }

        return $reflected;
    }

    /**
     * 3#
     *
     * @param ClassControllers $classControllers
     * @return ClassControllers
     */
    public function getControllerStacks(ClassControllers $classControllers): ClassControllers
    {
        foreach ($classControllers as $className => $stack) {
            $reflection = new ReflectionClass($className);
            // * creiamo diversi stack per popolarli all'interno di ClassController
            $StackOfControllerAttr = $this->GetAttributesOfController($reflection);
            $classControllers->setStack(className: $className, stack: $StackOfControllerAttr);
        }
        return $classControllers;
    }

    private function extractRoutes(ClassControllers $controllers): RouteCollection
    {
        $routes = new RouteCollection();

        foreach ($controllers->all() as $className => $stack) {
            // ottieni la reflection del controller
            $reflection = new ReflectionClass($className);
            // * Doesn't acceppt private and protected  methods | Non accetta metodi privati o protetti.
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {

                // Ignora costruttore e metodi ereditati da Controller base

                $routeAttributes = $method->getAttributes(RouteAttr::class);
                if ($method->isConstructor())
                    continue;
                if ($method->class !== $reflection->getName())
                    continue;

                // * if the public method don't have RouteAttr attribute, throw exception

                if (empty($routeAttributes)) {
                    throw new \Exception(
                        "The public method {$method->getName()} of class {$reflection->getName()} don't have a valid #[RouteAttr] attribute."
                    );
                }

                foreach ($routeAttributes as $attr) {
                    $route = $attr->newInstance();

                    // Path base ereditato dalla superclasse  + path del metodo
                    $basePath = implode('', $stack->Path()->toArray());
                    $fullPath = rtrim($basePath, '/') . $route->path;
                    $fullPath = preg_replace('#/+#', '/', $fullPath); // normalizza //

                    // Merge middleware controller + rotta
                    $middlewares = array_merge(
                        $stack->Middleware()->toArray(),
                        $route->middleware ?? []
                    );

                    // * inserisco la classe RouteDefintion nella collection RouteCollection

                    $routes->add(new RouteDefinition(
                        $fullPath,
                        strtoupper($route->method),
                        $className,
                        $method->getName(),
                        $route->name,
                        array_unique($middlewares)
                    ));
                }
            }
        }

        return $routes;
    }

    public function load(): RouteCollection
    {
        // *1 Rirtona tutti i controllers con namespace e file
        $controllers = $this->getAllControllers();

        // *2 Filtriamo tutte le classi controller eccetto astratte e interfacce
        $classControllers = $this->getReflrectedController($controllers);

        //  *3 Prepara lo stack (middleware/basePath) per ogni controller
        $controllerStacks = $this->getControllerStacks($classControllers);
        // $listOfPathAndMw = [];
        // foreach ($controllerStacks->all() as $class => $stack) {
        //     $listOfPathAndMw[$class] = $stack->toArray();
        // }
        // dd($listOfPathAndMw);
        // * Prepara e compila un array con tutte le rotte.
        return $this->extractRoutes($controllerStacks);
    }





    /**
     * 1 #
     * @param array<string,string> $controllersPath   percorso file (es. baseRoot()."/App/Controllers/*.php")
     * @return array<string,string> lista dei controller pronti: [namespace => file.php] 
     * 
     */
    private function getAllControllers(): array
    {
        $principalPath = $this->controllersPath;
        foreach ($principalPath as $namespace => $path) {
            // Scansiona ricorsivamente le sotto direcotry
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path)
            );

            foreach ($iterator as $file) {
                // controllo se il file è php
                if ($file->isFile() && $file->getExtension() === "php") {
                    //* Ottieni il percors relativo al path base ottenendo per esempio 'Admin\NameController.php"
                    $relative = str_replace($path . DIRECTORY_SEPARATOR, '', $file->getPathname());

                    // * Conversione del path in namespace per ogni file trovato all'interno dell'iterazione
                    $className = $namespace . '\\' . str_replace(
                        [DIRECTORY_SEPARATOR, '.php'], // rimuovo la slash e l'estensione php
                        ["\\", ""], // sostituisco slah con \\ e l'estensione con vuoto
                        $relative // path che contiene Admin/nameController
                    );


                    // * inserimento dell classname e del file
                    $controllers[$className] = $file->getPathname();
                }
            }
        }
        //* Ritorna un array pieno di [namespace => percorso completo + filecontroller.php] 
        return $controllers;
    }


    //     
    //      * 
    //      *  get attributes of any controller class
    //      *  @param \ReflectionClass $rc
    //      * @return array
    //      */
    private function GetAttributesOfController(ReflectionClass $rc): Stack
    {
        // Una classe che ha la collection di Middleware e di Path
        $stack = new Stack();

        // risali la gerarchia, prima le superclassi così l'ordine è coerente.
        $cursor = $rc;
        while ($cursor) {
            $chain[] = $cursor;
            $cursor = $cursor->getParentClass();
        }
        $chain = array_reverse($chain); //ordine dell'array: superclasse -> sottoclasse

        foreach ($chain as $classRef) {
            foreach ($classRef->getAttributes(ControllerAttr::class) as $controllerAttribute) {
                // * Gestione degli errori: se l'utente ha sbagliato a settare l'attributo
                try {
                    // crea una istanza dell'attributo
                    $instance = $controllerAttribute->newInstance();
                    // Unisce i middleware e opath trovati
                    if (property_exists($instance, 'middlewareNames') && !empty($instance->middlewareNames)) {
                        $stack->addMiddleware((array) $instance->middlewareNames);
                    }

                    if (property_exists($instance, 'basePath') && !empty($instance->basePath)) {
                        $stack->addPath($instance->basePath);
                    }
                } catch (LoaderAttributeException $e) {
                    $file = $classRef->getFileName();
                    $className = $classRef->getName();
                    $e->getFile();

                    throw new LoaderAttributeException(
                        "Invalid attribute usage in {$className} file: $file " .
                        "Expected constructor parameters for ControllerAttr are missing."
                    );
                }
            }
        }

        $stack->clean(); // rimuove duplicati

        return $stack;
    }
}
