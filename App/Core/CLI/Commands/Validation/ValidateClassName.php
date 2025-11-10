<?php

namespace App\Core\CLI\Commands\Validation;

use App\Core\CLI\System\Out;

class ValidateClassName
{

    public static function Validate(string $name,string $classEndName)
    {


        // 1. Controllo se inizia con numero
        if (preg_match('/^[0-9]/', $name)) {
            throw new \InvalidArgumentException("❌ The class name cannot start with a number.");
        }

        // 2. Controllo caratteri validi (solo lettere, numeri, underscore)
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) {
            throw new \InvalidArgumentException("❌ Invalid class name. Use only letters, numbers, and underscores.");
        }

        // 3. Suggerimento se non termina con 'Middleware'
        if (!str_ends_with($name, $classEndName)) {
            Out::warn("💡 Consider ending your class name with '$classEndName' for consistency.");
             throw new \InvalidArgumentException("❌ Invalid class name.");
        }

        // 4. Controllo se esiste già un file con quel nome
        $path = "App/Middleware/{$name}.php";
        if (file_exists($path)) {
            throw new \InvalidArgumentException("⚠️  A middleware named '$name' already exists.");
        }
    }
}
