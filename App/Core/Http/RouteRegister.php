<?php

namespace App\Core\Http;

use App\Core\Exception\NotFoundException;

/**
 * Summary of RouteRegister
 * Registro in-memory: Indicizza per HTTP method
 */
class RouteRegister
{
    private array $routeByRequest = [];

    /**
     * Summary of register
     * @param array $flatRoutes
     * @return void popola in questo modo
     *  'GET' => [
     *       [ 'path' => '/', 'controller' => 'HomeController', ... ],
     *       [ 'path' => '/law', 'controller' => 'HomeController', ... ],
     *   ],
     *   'POST' => [
     *      ...
     *      ]
     *   ]
     * 
     */
    public function register(array $flatRoutes): void
    {
        foreach ($flatRoutes as $route) {
            $method = strtoupper($route['method']); // preniamo il metodo, rendiamolo Upper per facilitare la vita a tutti ;) 
            $this->routeByRequest[$method][] = $route; // ogni metodo ha all'interno i metadati del controller, middleware, action etc
        }
    }

    /**
     * Summary of all
     * @return array restituisce tutte le registrazioni
     */
    public function all(): array
    {
        return $this->routeByRequest;
    }

    public function getByRequestMethod(Request $request){
       
        return $this->routeByRequest[$request->method]?? throw new NotFoundException("Route not fount by $request->path and method $request->method");;
    }
}
