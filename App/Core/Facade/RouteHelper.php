<?php

namespace App\Core\Facade;

use App\Core\Mvc;
use InvalidArgumentException;

class RouteHelper
{
    public static function getByName(string $name, array $params = []): string
    {
        $router = Mvc::$mvc->router;
        $route = $router->boot()->getByName($name); // ? qui deve tornare una RouteDefinition

        if (!$route) {
           throw new InvalidArgumentException("Route with name $name not found.");
        }



        $uri = $route->uri;

        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value ?? '', $uri);
        }
       // dd($uri);
       // $route->setParam($params);

        return $uri;
    }
}
