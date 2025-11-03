<?php
// App/Core/Http/RouteDispatcher.php
namespace App\Core\Http;

use App\Core\Contract\MiddlewareInterface;
use App\Core\Http\Helpers\RouteDefinition;
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


    public function dispatch(RouteDefinition $route)
    {
   
        // dd($route);

        // * Esegui middlewares ++
        $this->executeMiddleware($route->middleware);
        
        // * Prepara controller e azioni
        $controller =  new $route->controller(mvc());
        // $params     = $route['params' ?? [];


        $args = [mvc()->request];
        foreach ($route->getParams() as $item) {
            $args[] = $item;
        }

        // Le magie di php
        return call_user_func_array(callback: [$controller, $route->action], args: $args);
    }

    private function executeMiddleware(array $middlewareArray): void
    {
        foreach ($middlewareArray as $name) {
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
                $mw->exec(new Request()); // esegui middleware
            }
        }
    }
}
