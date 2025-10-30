<?php

namespace App\Core\Http;

/**
 * Summary of RouteRegister
 * Registro in-memory: Indicizza per HTTP method
 */
class RouteRegister
{
    private array $routesByMethos = [];

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
            $this->routesByMethos[$method][] = $route; // ogni metodo ha all'interno i metadati del controller, middleware, action etc
        }
    }

    /**
     * Summary of all
     * @return array restituisce tutte le registrazioni
     */
    public function all(): array
    {
        return $this->routesByMethos;
    }

    public function getMehtod(string $method){
       

        return $this->routesByMethos[$method];
    }
}
