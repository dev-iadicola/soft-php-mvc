<?php

namespace App\Core\Facade;



class RouteHelper
{
    public static function getByName(string $name, array $params = []): string
    {
        $router = mvc()->router;
        $route = $router->boot()->getByName($name); // ? qui deve tornare una RouteDefinition

        if (!$route) {
            echo "Route name '{$name}' not found";
        }



        $uri = $route->uri;

        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
        }

        return $uri;
    }
}
