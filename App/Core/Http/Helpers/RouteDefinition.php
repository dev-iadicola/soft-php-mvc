<?php

declare(strict_types=1);

namespace App\Core\Http\Helpers;

class RouteDefinition
{
    private array $params;

    public function __construct(
        public string $uri,
        public string $method,
        public string $controller,
        public string $action,
        public ?string $name = null,
        public array $middleware = [],
    ) {}

    public function findByMethod(string $method): bool
    {
        return (bool) ($this->method == $method);
    }

    /**
     * Summary of addParam
     *
     * @param  array  $params  we get it when make RouteMatcher
     */
    public function setParam(array $params): void
    {
        $this->params = $params;
    }

    public function getParams(?string $key = null): ?array
    {
            return $this->params;   
    }

    public function getParam(string $key): mixed{
           return $this->params[$key];
    }
}
