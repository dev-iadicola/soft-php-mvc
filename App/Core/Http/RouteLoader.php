<?php

namespace App\Core\Http;

use ReflectionClass;
use ReflectionMethod;
use ArgumentCountError;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use App\Core\Http\Collection\Stack;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Attributes\ControllerAttr;
use App\Core\Exception\LoaderAttributeException;

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

 


    public function load(): array
    {
        $routes = [];
        // * Rirtona tutti i controllers con namespace e file
        $controllers = $this->getAllControllers();

        foreach ($controllers as $className => $fileController) {
      
                // * reflection dei controller trovati
                $reflectionController = new ReflectionClass($className); // classname è una stringa del tipo App/Controller/UserController
                // if($reflectionController->isInstantiable()){

                // }
                // * path e middleware di ogni controllers (e classi parent)
                $classStack = $this->collectInheritedClassAttributes($reflectionController);

                /**
                 * Filtro per metodi solo pubblici all'interno di ogni controller.
                 */
                $publicMethodOfController = $reflectionController->getMethods(ReflectionMethod::IS_PUBLIC);
                foreach ($publicMethodOfController as $reflectionMethod) {
                    // * Prendi i Meotdi che hanno solo gli attributi #[RouteAttr]
                    $routeAttributes = $reflectionMethod->getAttributes(RouteAttr::class);
                    if (!$routeAttributes) continue; 

                    
                    $controllerAttribute = [];
                    foreach ($reflectionMethod->getAttributes(ControllerAttr::class) as $middlewareAttribute) {
                        $instance = $middlewareAttribute->newInstance(); // nuova instanza del AttributeMiddleware
                        // aggiungiamo l'istanza all'interno della lista di array presenti nei metodi del controller
                        $controllerAttribute['routes'] = array_merge($controllerAttribute['routes'], (array) $instance->middlewareNames);
                        $controllerAttribute['basepath'] = array_merge($controllerAttribute['basepath'], (array) $instance->basePath);
                    }

                    // Per ogni attributo Route sul metodo 
                    foreach ($routeAttributes as $attribute) {
                        /** @var RouteAttr $route */
                        $route = $attribute->newInstance();


                        $routes[] = [
                            'path'        => $route->path,
                            'method'      => strtoupper($route->method),
                            'controller'  => $className,
                            'action'      => $reflectionMethod->getName(),
                            'middlewares' => 'boh',
                            'name'        => $route->name,
                        ];
                    }
                }
            }

            return $routes;
        }

        private getControllerAttributeCollection(): Stack{
            $stack = new Stack();
        }



    /**
     * @param array<string,string> $controllersPath   percorso file (es. baseRoot()."/App/Controllers/*.php")
     * @return array<string,string> lista dei controller pronti: [namespace => file.php] 
     * 
     */
    private function getAllControllers(): array
    {
        $controllers = [];
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


    //     /**
    //      * Summary of collectInheritedClassAttributes
    //      *  get attributes of any controller class
    //      *  @param \ReflectionClass $rc
    //      * @return array
    //      */
    private  function collectInheritedClassAttributes(ReflectionClass $rc): Stack
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
                    if($instance->middlewareNames)
                        $stack->addMiddleware($instance->middlewareNames);
                    if($instance->basePath)
                        $stack->addPath($instance->basePath);
            
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

        $stack->clean();// rimuove duplicati

        return $stack;
    }
}
