<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;

class MaintenanceService
{
    public static function isEnabled(): bool
    {
        return strtolower((string) getenv('MAINTENANCE')) === 'true';
    }

    public static function enable(string $envPath): void
    {
        Config::updateEnv($envPath, 'MAINTENANCE', 'true');
    }

    public static function disable(string $envPath): void
    {
        Config::updateEnv($envPath, 'MAINTENANCE', 'false');
    }
}
