<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands\Route;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\Http\RouteLoader;

/**
 * Comando CLI: php soft route:list
 * Mostra tutte le rotte registrate in formato tabella.
 */
class RouteListCommand implements CommandInterface
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

        $allRoutes = $routes->all();

        if (empty($allRoutes)) {
            Out::info("No routes registered.");
            return;
        }

        // Prepara i dati per la tabella
        $rows = [];
        foreach ($allRoutes as $route) {
            $rows[] = [
                'method' => $route->method,
                'uri' => $route->uri,
                'name' => $route->name ?? '',
                'action' => $this->shortenClass($route->controller) . '@' . $route->action,
                'middleware' => implode(', ', $route->middleware),
            ];
        }

        // Ordina per URI
        usort($rows, fn(array $a, array $b) => strcmp($a['uri'], $b['uri']));

        // Calcola larghezza colonne
        $headers = ['Method', 'URI', 'Name', 'Controller@Action', 'Middleware'];
        $keys = ['method', 'uri', 'name', 'action', 'middleware'];
        $widths = [];

        foreach ($keys as $i => $key) {
            $widths[$i] = mb_strlen($headers[$i]);
            foreach ($rows as $row) {
                $widths[$i] = max($widths[$i], mb_strlen($row[$key]));
            }
        }

        // Stampa la tabella
        $separator = '+';
        foreach ($widths as $w) {
            $separator .= str_repeat('-', $w + 2) . '+';
        }

        echo $separator . PHP_EOL;

        // Header
        echo '|';
        foreach ($headers as $i => $header) {
            echo ' ' . str_pad($header, $widths[$i]) . ' |';
        }
        echo PHP_EOL;
        echo $separator . PHP_EOL;

        // Righe
        foreach ($rows as $row) {
            echo '|';
            foreach ($keys as $i => $key) {
                $value = $row[$key];
                // Colora il metodo HTTP
                if ($key === 'method') {
                    $value = $this->colorMethod($value, $widths[$i]);
                } else {
                    $value = str_pad($value, $widths[$i]);
                }
                echo ' ' . $value . ' |';
            }
            echo PHP_EOL;
        }

        echo $separator . PHP_EOL;

        Out::info("Total routes: " . count($rows));
    }

    /**
     * Accorcia il nome della classe rimuovendo il namespace comune.
     */
    private function shortenClass(string $fqcn): string
    {
        $prefixes = ['App\\Controllers\\', 'App\\Core\\Controllers\\'];
        foreach ($prefixes as $prefix) {
            if (str_starts_with($fqcn, $prefix)) {
                return substr($fqcn, strlen($prefix));
            }
        }
        return $fqcn;
    }

    /**
     * Colora il metodo HTTP per il terminale.
     */
    private function colorMethod(string $method, int $width): string
    {
        $padded = str_pad($method, $width);
        $colors = [
            'GET' => "\033[32m",    // green
            'POST' => "\033[33m",   // yellow
            'PUT' => "\033[34m",    // blue
            'PATCH' => "\033[36m",  // cyan
            'DELETE' => "\033[31m", // red
        ];
        $color = $colors[$method] ?? "\033[0m";
        return $color . $padded . "\033[0m";
    }
}
