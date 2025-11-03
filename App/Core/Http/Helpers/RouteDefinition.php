<?php

namespace App\Core\Http\Helpers;

use App\Core\Support\Collection;

class RouteDefinition
{

    private array $params; 
   
    public function __construct(
        public string $uri,
        public string $method,
        public string $controller,
        public string $action,
        public string|null $name = null ,
        public array $middleware = [],
    ) {}

 
    public function findByMethod(string $method):bool{
        if($this->method == $method)
            return true;
        
        return false;
    }

    /**
     * Summary of addParam
     * @param array $params we get it when make RouteMatcher 
     * @return void
     */
    public function setParam(array $params): void{
        $this->params = $params;
    }
    public function getParams(): array{
        return $this->params;
    }
    
}
