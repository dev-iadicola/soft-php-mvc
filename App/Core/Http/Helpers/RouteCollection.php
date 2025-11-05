<?php

namespace App\Core\Http\Helpers;

use Traversable;
use ArrayIterator;

class RouteCollection implements \IteratorAggregate
{
    /** @var RouteDefinition[] */
    private array $routes = [];

    public function add(RouteDefinition $route):static{
        $this->VeirfyDuplicateRoute($route);
        $this->routes[] = $route;
        return $this;
    }
    // * Verify that a controller does not have the same route and method of other controller
    // Verifica che piu' controllers non abbia la stessa rotta e stesso metodo
    private function VeirfyDuplicateRoute(RouteDefinition $route){
        foreach ($this->routes as $existing) {
        if ($existing->method === $route->method && $existing->uri === $route->uri) {
            throw new \RuntimeException(
                sprintf(
                    "Duplicate route detected for [%s %s]\nExisting: %s::%s\nNew: %s::%s",
                    $route->method,
                    $route->uri,
                    $existing->controller,
                    $existing->action,
                    $route->controller,
                    $route->action
                )
            );
        }
    }
    }

    public function all(): array{
        return $this->routes;
    }

    public function filter(string $method): RouteCollection{
        $newRoutes = new RouteCollection();
        foreach($this->routes as $route){
            if($route->findByMethod($method))
                $newRoutes->add($route);
        }
        return $newRoutes;
    }


    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->routes);
    }
}
