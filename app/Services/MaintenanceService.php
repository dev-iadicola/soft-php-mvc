<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Core\GetEnv;

class MaintenanceService
{
    public static function isEnabled(): bool
    {
        return GetEnv::bool('MAINTENANCE', false) ?? false;
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
