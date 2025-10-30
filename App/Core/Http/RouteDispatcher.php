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
    public function dispatch(array $route)
    {
        // 1) Esegui middleware
        foreach ($route['middlewares'] as $name) {
            $class = "App\\Core\\Middleware\\" . ucfirst($name) . "Middleware";
            if (!class_exists($class)) {
                throw new \RuntimeException("Middleware non trovato: $class");
            }
            $mw = new $class();
            if (!$mw instanceof \App\Core\Contract\MiddlewareInterface) {
                throw new \RuntimeException("$class deve implementare MiddlewareInterface");
            }
            $mw->exec();
        }

        // 2) Esegui controller
        $controller = new $route['controller']();
        $action     = $route['action'];
        $params     = $route['params'] ?? [];

        // Invocazione con parametri nominati (PHP 8): se vuoi, puoi anche mappare by-name → signature
        return call_user_func_array([$controller, $action], array_values($params));
    }
}
