<?php

declare(strict_types=1);

namespace App\Core\Http\Helpers;

use RuntimeException;

/**
 * Helper per la generazione di URL da nomi di rotta (reverse routing).
 */
class RouteHelper
{
    private static ?RouteCollection $collection = null;

    /**
     * Imposta la RouteCollection da usare per la risoluzione dei nomi.
     */
    public static function setRouteCollection(RouteCollection $collection): void
    {
        self::$collection = $collection;
    }

    /**
     * Genera un URL da un nome di rotta, sostituendo i parametri dinamici.
     *
     * @param string               $name   Nome della rotta (es: 'admin.dashboard')
     * @param array<string, mixed> $params Parametri da sostituire (es: ['id' => 42])
     *
     * @throws RuntimeException Se la rotta non esiste o se mancano parametri
     */
    public static function url(string $name, array $params = []): string
    {
        if (self::$collection === null) {
            throw new RuntimeException(
                "RouteHelper: RouteCollection not set. Call RouteHelper::setRouteCollection() first."
            );
        }

        $route = self::$collection->findByName($name);

        if ($route === null) {
            throw new RuntimeException(
                "RouteHelper: route named '{$name}' not found."
            );
        }

        $uri = $route->uri;

        // Sostituisci i parametri {param} nel path
        foreach ($params as $key => $value) {
            $placeholder = '{' . $key . '}';
            if (str_contains($uri, $placeholder)) {
                $uri = str_replace($placeholder, (string) $value, $uri);
                unset($params[$key]);
            }
        }

        // Verifica che non ci siano parametri non sostituiti
        if (preg_match('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $uri, $matches)) {
            throw new RuntimeException(
                "RouteHelper: missing parameter '{$matches[1]}' for route '{$name}' (uri: {$route->uri})."
            );
        }

        // Eventuali parametri extra come query string
        if (!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }

        return $uri;
    }
}
