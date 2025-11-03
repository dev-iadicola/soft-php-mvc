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
