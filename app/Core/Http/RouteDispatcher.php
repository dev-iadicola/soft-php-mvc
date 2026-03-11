<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Contract\MiddlewareInterface;
use App\Core\Http\Helpers\RouteDefinition;
use InvalidArgumentException;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

/**
 * Esegue in ordine:
 *  1) tutti i middleware (classe + metodo)
 *  2) il controller + action con i parametri mappati
 */
class RouteDispatcher
{
    public function dispatch(RouteDefinition $route): mixed
    {
        // Run middlewares
        $response = $this->executeMiddleware($route->middleware, $route->controller);

        if ($response) {
            return null;
        }

        // Controller
        $controller = new $route->controller();
        $method = $route->action;

        $reflection = new ReflectionMethod($controller, $method);
        $args = [];

        foreach ($reflection->getParameters() as $param) {
            $name = $param->getName();
            $type = $param->getType();

            // Request Injection
            if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                if ($type->getName() === Request::class) {
                    $args[] = mvc()->request;

                    continue;
                }
            }

            // Route Params
            if (array_key_exists($name, $route->getParams())) {
                $value = $route->getParam($name);
                if ($type instanceof ReflectionNamedType) {
                    settype($value, $type->getName());
                }

                $args[] = $value;

                continue;
            }

            // Degault Value
            if ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();

                continue;
            }

            throw new RuntimeException(
                "Missing route parameter '{$name}' for {$route->controller}::{$method}"
            );
        }
      

        return call_user_func_array([$controller, $method], $args);

    }

    private function executeMiddleware(array $middlewareArray, string $controllerClass): ?Response
    {
        $config = mvc()->config->get('middleware');

        foreach ($middlewareArray as $name) {
            // se non esiste nel config/middleware lancia l'eccezione.
            if ( ! array_key_exists($name, $config)) {
                throw new InvalidArgumentException(
                    "Key '{$name}' not found in config/middleware.php. If it's a typo, please check your controller {$controllerClass} or its parent class " . get_parent_class($controllerClass) . '.'
                );
            }

            // * contiene una lista di middleware selezionati secondo il nome scelto e messo nel controller.
            $mwList = $config[$name];

            foreach ($mwList as $stringClass) {
                $mw = new $stringClass(mvc());
                if ( ! $mw instanceof MiddlewareInterface) {
                    throw new RuntimeException("{$stringClass} must implement MiddlewareInterface");
                }
                $response = $mw->exec(mvc()->request); // execute middleware
                // if middleware has return value
                if ($response) {
                    return $response;

                }
            }
        }

        return null;
    }
}
