<?php
// App/Core/Http/RouteDispatcher.php
namespace App\Core\Http;

use App\Core\Http\Response;
use InvalidArgumentException;
use App\Core\Contract\MiddlewareInterface;
use App\Core\Http\Helpers\RouteDefinition;

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

        // * Esegui middlewares 
        $response = $this->executeMiddleware($route->middleware);
        if ($response) {
            if (method_exists($response, 'send')) {
                $response->send();
            }
            return; // FERMA il flusso prima del controller
        }


        // * Prepara controller e azioni
        $controller =  new $route->controller(mvc());
      


        $args = [mvc()->request];
        foreach ($route->getParams() as $item) {
            $args[] = $item;
        }

        // Le magie di php
        return call_user_func_array(callback: [$controller, $route->action], args: $args);
    }

    private function executeMiddleware(array $middlewareArray): Response|null
    {

         $config = mvc()->config->middleware;

        foreach ($middlewareArray as $name) {
           
            // se non esiste nel config/middleware lancia l'eccezione.
            if (!array_key_exists($name, $config)) {
                throw new InvalidArgumentException(
                    "Key '$name' not found in config/middleware.php. If it's a typo, please check your controller {$this->controller} or its parent class " . get_parent_class($this->controller) . "."
                );
            }

            // * contiene una lista di middleware selezionati secondo il nome scelto e messo nel controller. 
             $mwList =  $config[$name];
            
            foreach ($mwList as $stringClass) {
                $mw = new $stringClass(mvc());
                if (!$mw instanceof MiddlewareInterface) {
                    throw new \RuntimeException("$stringClass must implement MiddlewareInterface");
                }
                $response = $mw->exec(mvc()->request); // execute middleware
                // if middleware has return value
                if ($response) {
                   return $response;

                }
            }
        }
        return null;
    }
}
