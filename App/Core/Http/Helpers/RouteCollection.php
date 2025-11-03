<?php

namespace App\Core\Http\Helpers;

use Traversable;
use ArrayIterator;

class RouteCollection implements \IteratorAggregate
{
    /** @var RouteDefinition[] */
    private array $routes = [];

    public function add(RouteDefinition $route):static{
        $this->routes[] = $route;
        return $this;
    }

    public function all(): array{
        return $this->routes;
    }

    public function findByMethodAndUri(string $method, string $uri): RouteDefinition|null{
        foreach($this->routes as $route){
            if($route->matches($method, $uri))
                return $route;
        }
        return null;
    }


    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->routes);
    }
}
