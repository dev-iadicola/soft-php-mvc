<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class GetEnv
{
    public static function raw(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);

        return $value === false ? $default : $value;
    }

    public static function string(string $key, ?string $default = null): ?string
    {
        $value = self::raw($key, $default);

        if ($value === null) {
            return null;
        }

        return trim((string) $value);
    }

    public static function bool(string $key, ?bool $default = null): ?bool
    {
        $value = self::raw($key, $default);

        if (is_bool($value) || $value === null) {
            return $value;
        }

        $normalized = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($normalized === null) {
            throw new RuntimeException("Environment variable {$key} cannot be cast to bool.");
        }

        return $normalized;
    }

    public static function int(string $key, ?int $default = null): ?int
    {
        $value = self::raw($key, $default);

        if (is_int($value) || $value === null) {
            return $value;
        }

        $normalized = filter_var($value, FILTER_VALIDATE_INT);

        if ($normalized === false) {
            throw new RuntimeException("Environment variable {$key} cannot be cast to int.");
        }

        return $normalized;
    }

    public static function float(string $key, ?float $default = null): ?float
    {
        $value = self::raw($key, $default);

        if (is_float($value) || $value === null) {
            return $value;
        }

        $normalized = filter_var($value, FILTER_VALIDATE_FLOAT);

        if ($normalized === false) {
            throw new RuntimeException("Environment variable {$key} cannot be cast to float.");
        }

        return $normalized;
    }

    public static function requiredString(string $key): string
    {
        $value = self::string($key);

        if ($value === null || $value === '') {
            throw new RuntimeException("Missing required environment variable {$key}.");
        }

        return $value;
    }
}
