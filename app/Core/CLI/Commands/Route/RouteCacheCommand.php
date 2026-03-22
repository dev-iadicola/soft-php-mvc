<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands\Route;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\Http\RouteCache;
use App\Core\Http\RouteLoader;

/**
 * Comando CLI: php soft route:cache
 * Compila e salva la cache delle rotte.
 */
class RouteCacheCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        $controllerPaths = require baseRoot() . '/config/controllers.php';
        $loader = new RouteLoader($controllerPaths);

        try {
            $routes = $loader->load();
        } catch (\Throwable $e) {
            Out::error("Failed to load routes: " . $e->getMessage());
            return;
        }

        $count = count($routes->all());

        if ($count === 0) {
            Out::warning("No routes found to cache.");
            return;
        }

        try {
            $path = RouteCache::cache($routes);
        } catch (\Throwable $e) {
            Out::error("Failed to write route cache: " . $e->getMessage());
            return;
        }

        Out::success("Route cache created successfully ({$count} routes).");
        Out::info("Cache file: {$path}");
    }
}
