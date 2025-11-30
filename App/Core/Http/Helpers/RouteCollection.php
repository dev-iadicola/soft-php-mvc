<?php

namespace App\Core\Http\Helpers;

use Traversable;
use ArrayIterator;

class RouteCollection implements \IteratorAggregate
{
    /** @var RouteDefinition[] */
    private array $routes = []; //rotta
    private array $namedRoutes = []; // name che diamo alla rotta

    public function getByName(string $name): RouteDefinition{
       return $this->namedRoutes[$name];
    }
    public function add(RouteDefinition $route): static
    {
        $this->VeirfyDuplicateRoute($route);
        $this->routes[] = $route;
        if(!empty($route->name)){
            $this->namedRoutes[$route->name] = $route;
        }

       $this->verificationMethodAllowedOrFail($route);
        return $this;
    }

    private function verificationMethodAllowedOrFail(RouteDefinition $route){
        if(!in_array(strtoupper($route->method), ['GET','POST','PUT','PATCH','DELETE'])){
           throw new \InvalidArgumentException("
            The HTTP method '{$route->method}' is not allowed, correct your controller {$route->controller} : {$route->action}.
            Allowed methods are: GET, POST, PUT, PATCH, DELETE.");
        }
    }
    // * Verify that a controller does not have the same route and method of other controller
    // Verifica che piu' controllers non abbia la stessa rotta e stesso metodo
    private function VeirfyDuplicateRoute(RouteDefinition $route)
    {
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

    public function all(): array
    {
        return $this->routes;
    }

    public function filter(string $method): RouteCollection
    {
        $newRoutes = new RouteCollection();
        foreach ($this->routes as $route) {
            if ($route->findByMethod($method))
                $newRoutes->add($route);
        }
        return $newRoutes;
    }


    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->routes);
    }
}
