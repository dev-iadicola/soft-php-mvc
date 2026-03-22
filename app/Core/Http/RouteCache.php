<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Http\Helpers\RouteCollection;
use App\Core\Http\Helpers\RouteDefinition;
use RuntimeException;

/**
 * RouteCache — Serializza/deserializza le rotte in un file cache
 * per evitare la reflection in produzione.
 */
class RouteCache
{
    private const CACHE_DIR = '/storage/cache';
    private const CACHE_FILE = 'routes.cache.php';

    /**
     * Ritorna il path completo del file cache.
     */
    public static function getCachePath(): string
    {
        return baseRoot() . self::CACHE_DIR . '/' . self::CACHE_FILE;
    }

    /**
     * Serializza la RouteCollection nel file cache.
     */
    public static function cache(RouteCollection $routes): string
    {
        $cachePath = self::getCachePath();
        $cacheDir = dirname($cachePath);

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $data = [];
        foreach ($routes->all() as $route) {
            $data[] = [
                'uri' => $route->uri,
                'method' => $route->method,
                'controller' => $route->controller,
                'action' => $route->action,
                'name' => $route->name,
                'middleware' => $route->middleware,
            ];
        }

        $content = '<?php return ' . var_export($data, true) . ';' . PHP_EOL;

        if (file_put_contents($cachePath, $content) === false) {
            throw new RuntimeException("Failed to write route cache to: {$cachePath}");
        }

        return $cachePath;
    }

    /**
     * Carica la RouteCollection dal file cache.
     *
     * @throws RuntimeException se il file non esiste
     */
    public static function loadFromFile(string $cachePath): RouteCollection
    {
        if (!file_exists($cachePath)) {
            throw new RuntimeException("Route cache file not found: {$cachePath}");
        }

        $data = require $cachePath;

        if (!is_array($data)) {
            throw new RuntimeException("Invalid route cache format in: {$cachePath}");
        }

        $collection = new RouteCollection();

        foreach ($data as $entry) {
            $collection->add(new RouteDefinition(
                uri: $entry['uri'],
                method: $entry['method'],
                controller: $entry['controller'],
                action: $entry['action'],
                name: $entry['name'] ?? null,
                middleware: $entry['middleware'] ?? [],
            ));
        }

        return $collection;
    }

    /**
     * Verifica se il file cache esiste.
     */
    public static function exists(): bool
    {
        return file_exists(self::getCachePath());
    }

    /**
     * Elimina il file cache.
     */
    public static function clear(): bool
    {
        $path = self::getCachePath();

        if (file_exists($path)) {
            return unlink($path);
        }

        return true;
    }
}
