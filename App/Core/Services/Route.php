<?php
namespace App\Core\Services;
use App\Core\Http\Request;

/**
 * Summary of Route
 * @deprecated 
 */
class Route {

    private static array $routes = [
        'get' => [],
        'post' => [],
        'delete' => []
    ];

    public static function get(string $path, string $controller, string $method): void {
        static::$routes['get'][$path] = [$controller, $method];
    }

    public static function post(string $path, string $controller, string $method): void {
        static::$routes['post'][$path] = [$controller, $method];
    }


    public static function delete(string $path, string $controller, string $method): void {

        static::$routes['post'][$path] = [$controller, $method];
    }

    public static function all(): array {
        return self::$routes;
    }

    

}