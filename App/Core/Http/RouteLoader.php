<?php

namespace App\Core\Http;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use App\Core\Http\Attributes\AttributeMiddleware;
use App\Core\Http\Attributes\AttributeRoute;
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
     * Summary of __construct
     * @param array @param array<string,string> $controllersPath  percorso files dei controller con chiave Namespace 
     * ossia array: key:App\\Controllers => value:[percorso completo]/App/Controllers 
     */
    public function __construct(private array $_controllersPath) {}

    /**
     * @param array<string,string> $controllersPath   percorso file (es. baseRoot()."/App/Controllers/*.php")
     * @return array<string,string> lista dei controller pronti: [namespace => file.php] 
     * 
     */
    private function getAllControllers(): array
    {
        $controllers = [];
        $principalPath = $this->_controllersPath;
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

                    // * Conversione del path in namespace 
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
    public function load(): array
    {
        $routes = [];
        // * Rirtona tutti i controllers con namespace e file
        $controllers = $this->getAllControllers();

        foreach ($controllers as $className => $fileController) {
            // inclusione del file se non esiste già
            if (!class_exists($className)) {
                require_once $fileController;
            }
            // * reflection del controller 
            $reflectionController = new ReflectionClass($className); // classname è una stringa del tipo App/Controller/UserController

            // * ignora classi astratte o interfaccie
            if ( ! $reflectionController->isInstantiable() || $reflectionController->isAbstract()) continue;

            // * Middleware ereditati (classi parent)
            $classMiddlewares = self::collectInheritedClassMiddlewares($reflectionController);

            foreach ($reflectionController->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
                $routeAttributes = $reflectionMethod->getAttributes(AttributeRoute::class);

                // se non ha attributi di Route, salta/ignora 
                if (!$routeAttributes) continue;

                // * Middleware definiti sul metodo del controller 
                $middlewaresInMethodsController = [];
                foreach ($reflectionMethod->getAttributes(AttributeMiddleware::class) as $middlewareAttribute) {
                    $instance = $middlewareAttribute->newInstance(); // nuova instanza del AttributeMiddleware
                    // aggiungiamo l'istanza all'interno della lista di array presenti nei metodi del controller
                    $middlewaresInMethodsController = array_merge($middlewaresInMethodsController, (array) $instance->names);
                }

                // Per ogni attributo Route sul metodo 
                foreach ($routeAttributes as $attribute) {
                    /** @var AttributeRoute $route */
                    $route = $attribute->newInstance();
                  
                 
                    $routes[] = [
                        'path'        => $route->path,
                        'method'      => strtoupper($route->method),
                        'controller'  => $className,
                        'action'      => $reflectionMethod->getName(),
                        'middlewares' => array_values(array_unique(array_merge($classMiddlewares, $middlewaresInMethodsController))),
                        'name'        => $route->name,
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
            foreach ($classRef->getAttributes(AttributeMiddleware::class) as $middlewareAttribute) {
                $instance = $middlewareAttribute->newInstance();
                $stack = array_merge($stack, (array) $instance->names);
            }
        }

        return array_values(array_unique($stack));
    }
}
