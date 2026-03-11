<?php

declare(strict_types=1);

namespace App\Core\Support\Collection;

class ConfigCollection
{
    protected array $attributes = [];

    protected string $basePath;

    public function __construct(array $files)
    {
        $this->attributes = $files;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (!str_contains($key, '.')) {
            return $this->attributes[$key] ?? $default;
        }

        $segments = explode('.', $key);
        $value = $this->attributes;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function all(): array
    {
        return $this->attributes;
    }
}
