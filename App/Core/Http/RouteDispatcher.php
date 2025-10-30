<?php
// App/Core/Http/RouteDispatcher.php
namespace App\Core\Http;

use App\Core\Contract\MiddlewareInterface as ContractMiddlewareInterface;
use App\Core\Middleware\MiddlewareInterface;

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
    private array $middlewares;

    private ?string $name = null;

    private RouteDispatcher $dispatche;


    public function dispatch(array $route)
    {
        $this->path = $route['path'];
        $this->method = $route['method'];
        $this->action = $route['action'];
        $this->controller = $route['controller'];
        $this->middlewares = $route['middlewares'];
        $this->name = $route['name'];


        // * Esegui middlewares ++
        foreach ($this->middlewares as $name) {
            $class = "App\\Core\\Middleware\\" . ucfirst($name) . "Middleware";
            if (!class_exists($class)) {
                throw new \RuntimeException("Middleware non trovato: $class");
            }
            $mw = new $class();
            if (!$mw instanceof \App\Core\Contract\MiddlewareInterface) {
                throw new \RuntimeException("$class deve implementare MiddlewareInterface");
            }
            $mw->exec(); // esegui middleware
        }

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
}
