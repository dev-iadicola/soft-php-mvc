<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands\Validation;

class ValidateClassName
{
    public static function Validate(string $name, string $classEndName): void
    {
        // 1. Check if starts with a number
        if (preg_match('/^[0-9]/', $name)) {
            throw new \InvalidArgumentException("The class name cannot start with a number.");
        }

        // 2. Check valid characters (only letters, numbers, underscore)
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) {
            throw new \InvalidArgumentException("Invalid class name. Use only letters, numbers, and underscores.");
        }

        // 3. Check if ends with the expected suffix
        if (!str_ends_with($name, $classEndName)) {
            throw new \InvalidArgumentException("Class name should end with '{$classEndName}' for consistency.");
        }
    }
}
