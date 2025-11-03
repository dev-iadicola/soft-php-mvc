<?php

namespace App\Core\Http\Helpers;

use App\Core\Support\Collection;

class RouteDefinition
{



    public function __construct(
        public string $uri,
        public string $method,
        public string $controller,
        public string $action,
        public string|null $name = null ,
        public array $middleware = [],
    ) {}

    public function matches(string $methodOrName, string $uri): bool
    {
        if ($this->method == $methodOrName && $this->uri == $uri) {
            return true;
        }
        return false;
    }
}
