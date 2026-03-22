<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Exception\NotFoundException;
use App\Core\Http\Helpers\RouteCollection;

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
    public function register(RouteCollection $routeCollection): void
    {
        foreach ($routeCollection as $route) {
            $method = $route->method;
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

    public function getByRequestMethod(Request $request): array {
       
        $method = $request->method();
        $path = $request->path();
        return $this->routeByRequest[$method] ?? throw new NotFoundException("Route not found by $path and method $method");
    }
}
