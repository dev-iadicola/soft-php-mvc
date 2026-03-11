<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands\Route;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\Http\RouteLoader;

/**
 * Comando CLI: php soft route:list
 * Mostra tutte le rotte registrate in formato tabella.
 *
 * Opzioni:
 *   --method=GET       Filtra per metodo HTTP (GET, POST, PUT, PATCH, DELETE)
 *   --path=pattern     Filtra per pattern URI (match parziale, case-insensitive)
 */
class RouteListCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        $methodFilter = $this->parseOption($command, '--method');
        $pathFilter = $this->parseOption($command, '--path');

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
            if ($methodFilter !== null && strtoupper($route->method) !== strtoupper($methodFilter)) {
                continue;
            }

            if ($pathFilter !== null && stripos($route->uri, $pathFilter) === false) {
                continue;
            }

            $rows[] = [
                'method'     => $route->method,
                'uri'        => $route->uri,
                'name'       => $route->name ?? '',
                'controller' => $this->shortenClass($route->controller),
                'action'     => $route->action,
                'middleware'  => implode(', ', $route->middleware),
            ];
        }

        if (empty($rows)) {
            $msg = "No routes found";
            $filters = [];
            if ($methodFilter !== null) {
                $filters[] = "method=" . strtoupper($methodFilter);
            }
            if ($pathFilter !== null) {
                $filters[] = "path={$pathFilter}";
            }
            if (!empty($filters)) {
                $msg .= " matching filters: " . implode(', ', $filters);
            }
            Out::info($msg . ".");
            return;
        }

        // Ordina per URI, poi per metodo
        usort($rows, function (array $a, array $b): int {
            $cmp = strcmp($a['uri'], $b['uri']);
            return $cmp !== 0 ? $cmp : strcmp($a['method'], $b['method']);
        });

        $this->printTable($rows);

        $filterInfo = '';
        if ($methodFilter !== null || $pathFilter !== null) {
            $parts = [];
            if ($methodFilter !== null) {
                $parts[] = "method=" . strtoupper($methodFilter);
            }
            if ($pathFilter !== null) {
                $parts[] = "path={$pathFilter}";
            }
            $filterInfo = ' (filtered by ' . implode(', ', $parts) . ')';
        }

        Out::info("Total routes: " . count($rows) . $filterInfo);
    }

    /**
     * Stampa una tabella formattata nel terminale.
     *
     * @param array<int, array<string, string>> $rows
     */
    private function printTable(array $rows): void
    {
        $headers = ['Method', 'URI', 'Name', 'Controller', 'Action', 'Middleware'];
        $keys    = ['method', 'uri', 'name', 'controller', 'action', 'middleware'];

        // Calcola larghezza colonne
        $widths = [];
        foreach ($keys as $i => $key) {
            $widths[$i] = mb_strlen($headers[$i]);
            foreach ($rows as $row) {
                $widths[$i] = max($widths[$i], mb_strlen($row[$key]));
            }
        }

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
    }

    /**
     * Estrae il valore di un'opzione CLI dal formato --key=value o --key value.
     */
    private function parseOption(array $args, string $option): ?string
    {
        foreach ($args as $i => $arg) {
            // Formato --key=value
            if (str_starts_with($arg, $option . '=')) {
                return substr($arg, strlen($option) + 1);
            }

            // Formato --key value
            if ($arg === $option && isset($args[$i + 1]) && !str_starts_with($args[$i + 1], '--')) {
                return $args[$i + 1];
            }
        }

        return null;
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
            'GET'    => "\033[32m",  // green
            'POST'   => "\033[33m",  // yellow
            'PUT'    => "\033[34m",  // blue
            'PATCH'  => "\033[36m",  // cyan
            'DELETE' => "\033[31m",  // red
        ];
        $color = $colors[$method] ?? "\033[0m";
        return $color . $padded . "\033[0m";
    }
}
