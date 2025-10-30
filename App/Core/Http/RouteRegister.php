<?php 
namespace App\Core\Http;
/**
 * Summary of RouteRegister
 * Registro in-memory: Indicizza per HTTP method
 */
class RouteRegister{
    private array $routesByMethos = [];
    
    public function register(array $flatRoutes): void{
        foreach($flatRoutes as $route){
            $method = $route['method']; // cio che abbiamo popolato su RouteLoader
            $this->routesByMethos[$method][]=$route;
        }
    }

    public function all():array{
        return $this->routesByMethos;
    }
}