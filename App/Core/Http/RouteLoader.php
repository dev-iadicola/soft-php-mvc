<?php

namespace App\Core\Http;

use ReflectionClass;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Route;
use ReflectionMethod;

/**
 * Summary of RouterLoader
 * 
 * Legge i controller, estre attributi, Route/Middleware a livello di clase di metodo e metodo
 * e produce una lista piatta di definizioni rotta pronte alla registrazione.
 * 
 */
class RouteLoader
{

    /**
     * @param string   $controllersPath   percorso file (es. baseRoot()."/App/Controllers/*.php")
     * @param string   $controllerNameSpace      namespace base (es. "App\\Controllers")
     * @return array[] lista di rotte pronte: [
     *   'path' => '/users/{id}',
     *   'method' => 'GET',
     *   'controller' => 'App\Controllers\UserController',
     *   'action' => 'show',
     *   'middlewares' => ['auth','admin'],
     *   'name' => 'users.show'
     * ]
     */
    public static function load(string $controllersPath, string $controllerNameSpace): array
    {
        $routes = [];
        foreach (glob($controllersPath) as $fileController) {
            require_once $fileController; // includo il file (una sola volta)
            $className = $controllerNameSpace . "\\" . basename($fileController, '.php');
            dump($className);

            // se non esiste la classe, ignoro
            if (!class_exists($className)) {
                continue;
            }
            // prendo il pieno controllo della classe
            $rc = new ReflectionClass($className);

            /**
             * * Middleware ereditatidalla gerarchia (superclassi / classi padre);
             * Consentendomi di avere la possibilità di ereditare i middleware delle classi padri, meno codice da applicare al controller
             */
            $classMiddlewares = self::collectInheritedClassMiddlewares($rc);
            // Metodi con #[Route]
            foreach($rc->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod){
                $routeAttributes = $reflectionMethod->getAttributes(Route::class);
                if(!$routeAttributes) continue; 

                // Middleware definiti sul metodo
                $methodMiddlewares = [];
                foreach($reflectionMethod->getAttributes(Middleware::class) as $middlewareAttributes){
                    $instance = $middlewareAttributes->newInstance();
                    $methodMiddlewares = array_merge($methodMiddlewares, (array) $instance->names);
                }
                foreach($routeAttributes as $attribute){
                    /** @var Route $r */
                    $route = $attribute->newInstance();
                    // * Popolazione delle route dentro l'array
                    $routes[] = [
                        'path' => $route->path,
                        'method' => strtoupper($route->method),
                        'controller' => $className,
                        'action' => $reflectionMethod->getName(),
                        // Unione: Gerarchia classe + metodi
                        'middlewares' => array_values(array_unique(array_merge($classMiddlewares, $methodMws))),
                        'name' => $route->name,
                    ];
 

                }
            }

           
        }
        return $routes;
    }

    private static function collectInheritedClassMiddlewares(ReflectionClass $rc): array
    {
        $stack = [];
        // risali la gerarchia, prima le superclassi così l'ordine è coerente.
        $cursor = $rc;
        while ($cursor) {
            $chain[] = $cursor;
            $cursor = $cursor->getParentClass();
        }
        $chain = array_reverse($chain); //ordine dell'array: superclasse -> sottoclasse

        foreach ($chain as $classRef) {
            foreach ($classRef->getAttributes(Middleware::class) as $middlewareAttribute) {
                $instance = $middlewareAttribute->newInstance();
                $stack = array_merge($stack, (array) $instance->names);
            }
        }

        return array_values(array_unique($stack));
    }
}
