<?php

declare(strict_types=1);

namespace App\Utils;

use App\Core\GetEnv;

/**
 * Class Enviroment
 *
 * Centralizes all environment variables and converts them to correct data types.
 * Centralizza tutte le variabili d’ambiente e le converte nel tipo di dato corretto.
 */
class Enviroment
{
    // Esempi di costanti leggibili
    public const DEBUG        = 'APP_DEBUG';
    public const APP_DEBUG        = 'APP_DEBUG';
    public const ENVIRONMENT  = 'APP_ENV';
    public const CLOUD        = 'CLOUD';
    public const EMAIL        = 'APP_EMAIL';
    public const MAINTENANCE  = 'MAINTENANCE';

    /**
     * Get a variable from the environment and normalize its type.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = GetEnv::raw($key, $default);

        if (is_string($value)) {
            $trimmed = trim($value);

            if (in_array(strtolower($trimmed), ['true', 'false', '1', '0', 'on', 'off'], true)) {
                return GetEnv::bool($key, is_bool($default) ? $default : null);
            }

            if (filter_var($trimmed, FILTER_VALIDATE_INT) !== false) {
                return GetEnv::int($key, is_int($default) ? $default : null);
            }

            if (filter_var($trimmed, FILTER_VALIDATE_FLOAT) !== false) {
                return GetEnv::float($key, is_float($default) ? $default : null);
            }

            return $trimmed;
        }

        return $value;
    }

    /**
     * Shortcuts for fast access
     */
    public static function isDebug(): bool
    {
        return (bool) self::get(key: self::APP_DEBUG, default: false);
    }
    public static function isProd(): bool
    {
        return (bool) self::get(key: self::APP_DEBUG, default: false);
    }

    public static function isMaintenance(): bool
    {
        return (bool) self::get(self::MAINTENANCE, false);
    }

    public static function email(): ?string
    {
        return self::get(self::EMAIL);
    }
}
