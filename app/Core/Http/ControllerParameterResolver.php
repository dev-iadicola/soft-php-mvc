<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Http\Helpers\RouteDefinition;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

class ControllerParameterResolver
{
    /**
     * @return array<int, mixed>
     */
    public function resolve(ReflectionMethod $reflection, RouteDefinition $route, ?Request $request = null): array
    {
        $args = [];
        $routeParams = $route->getParams() ?? [];
        $consumedRouteParams = [];
        $deferredIndexes = [];

        foreach ($reflection->getParameters() as $index => $param) {
            $name = $param->getName();
            $type = $param->getType();

            if ($type instanceof ReflectionNamedType && ! $type->isBuiltin() && $type->getName() === Request::class) {
                if (! $request instanceof Request) {
                    throw new RuntimeException(
                        "Unable to resolve Request for {$route->controller}::{$route->action} without an active request instance"
                    );
                }

                $args[$index] = $request;
                continue;
            }

            if (array_key_exists($name, $routeParams)) {
                $value = $route->getParam($name);

                if ($type instanceof ReflectionNamedType) {
                    settype($value, $type->getName());
                }

                $args[$index] = $value;
                $consumedRouteParams[$name] = true;
                continue;
            }

            $deferredIndexes[] = $index;
        }

        $remainingRouteParams = [];
        foreach ($routeParams as $key => $value) {
            if (!isset($consumedRouteParams[$key])) {
                $remainingRouteParams[$key] = $value;
            }
        }

        foreach ($deferredIndexes as $index) {
            $param = $reflection->getParameters()[$index];
            $name = $param->getName();
            $type = $param->getType();

            if ($remainingRouteParams !== [] && $this->canConsumeRouteValue($type)) {
                $routeKey = array_key_first($remainingRouteParams);
                $value = $remainingRouteParams[$routeKey];
                unset($remainingRouteParams[$routeKey]);

                if ($type instanceof ReflectionNamedType) {
                    settype($value, $type->getName());
                }

                $args[$index] = $value;
                continue;
            }

            if ($param->isDefaultValueAvailable()) {
                $args[$index] = $param->getDefaultValue();
                continue;
            }

            throw new RuntimeException(
                "Missing route parameter '{$name}' for {$route->controller}::{$route->action}"
            );
        }

        ksort($args);

        return array_values($args);
    }

    private function canConsumeRouteValue(?\ReflectionType $type): bool
    {
        if (!$type instanceof ReflectionNamedType) {
            return true;
        }

        return $type->isBuiltin();
    }
}
