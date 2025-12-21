<?php

namespace App\Utils;

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
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        //  Normalizza i boolean (true/false)
        if (in_array(strtolower($value), ['true', 'false', '1', '0', 'on', 'off'], true)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        // Se è numerico, convertilo in int o float
        if (is_numeric($value)) {
            return str_contains($value, '.') ? (float) $value : (int) $value;
        }

        // Altrimenti ritorna stringa pura
        return trim($value);
    }

    /**
     * Shortcuts for fast access
     */
    public static function isDebug(): bool
    {
        return self::get(key: self::APP_DEBUG, default: false);
    }
    public static function isProd():bool{
        return self::get(key: self::APP_DEBUG, default: false);
    }

    public static function isMaintenance(): bool
    {
        return self::get(self::MAINTENANCE, false);
    }

    public static function email(): ?string
    {
        return self::get(self::EMAIL);
    }
}
