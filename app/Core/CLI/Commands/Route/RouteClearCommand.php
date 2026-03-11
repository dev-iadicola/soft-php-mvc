<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands\Route;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\Http\RouteCache;

/**
 * Comando CLI: php soft route:clear
 * Elimina il file cache delle rotte.
 */
class RouteClearCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        if (!RouteCache::exists()) {
            Out::info("Route cache file does not exist. Nothing to clear.");
            return;
        }

        if (RouteCache::clear()) {
            Out::success("Route cache cleared successfully.");
        } else {
            Out::error("Failed to delete route cache file: " . RouteCache::getCachePath());
        }
    }
}
