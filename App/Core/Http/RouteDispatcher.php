<?php
// App/Core/Http/RouteDispatcher.php
namespace App\Core\Http;

use App\Core\Contract\MiddlewareInterface;
use InvalidArgumentException;

/**
 * Esegue in ordine:
 *  1) tutti i middleware (classe + metodo)
 *  2) il controller + action con i parametri mappati
 */
class RouteDispatcher
{
    private string $path;
    private string $method;
    private string $action;

    private string $controller;
    private array $nameOfListMiddleware;

    private ?string $name = null;

    private RouteDispatcher $dispatche;


    public function dispatch(array $route)
    {
        $this->method = $route['method'];
        $this->action = $route['action'];
        $this->controller = $route['controller'];
        $this->nameOfListMiddleware = $route['middlewares'];
        $this->name = $route['name'];


        // * Esegui middlewares ++
        $this->executeMiddleware();
        
        // * Prepara controller e azioni
        $controller =  new $this->controller(mvc());
        $params     = $route['params'] ?? [];


        $args = [mvc()->request];
        foreach ($params as $item) {
            $args[] = $item;
        }

        // Le magie di php
        return call_user_func_array(callback: [$controller, $this->action], args: $args);
    }

    private function executeMiddleware(): void
    {
        foreach ($this->nameOfListMiddleware as $name) {
            $middlewareArray = mvc()->config->middleware;

            // se non esiste nel config/middleware lancia l'eccezione.
            if (!array_key_exists($name, $middlewareArray)) {
                throw new InvalidArgumentException(
                    "Key '$name' not found in config/middleware.php. If it's a typo, please check your controller {$this->controller} or its parent class " . get_parent_class($this->controller) . "."
                );
            }

            // * contiene una lista di middleware selezionati secondo il nome scelto e messo nel controller. 
            $mwList =  $middlewareArray[$name];

            foreach ($mwList as $stringClass) {
               $mw = new $stringClass(mvc());
                if (!$mw instanceof MiddlewareInterface) {
                    throw new \RuntimeException("$$stringClass must implement MiddlewareInterface");
                }
                $mw->exec(); // esegui middleware
            }
        }
    }
}
