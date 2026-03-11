<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\CLI\Stubs\StubGenerator;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\Helpers\Str;

class MakeServiceCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        $name = $command[2] ?? null;

        if (!$name || str_starts_with($name, '-')) {
            Out::error('You must specify a service name. Example: php soft make:service UserService');
            return;
        }

        try {
            $className = Str::studly($name);

            if (!str_ends_with($className, 'Service')) {
                $className .= 'Service';
            }

            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $className)) {
                Out::error('Invalid service name. Use only letters, numbers, and underscores.');
                return;
            }

            $this->createService($className);
        } catch (\Throwable $e) {
            Out::error("Failed to create service: {$e->getMessage()}");
        }
    }

    public function createService(string $className): void
    {
        $filePath = getcwd() . '/app/Services/' . $className . '.php';

        $saved = StubGenerator::make('service')
            ->replace(['{{CLASS}}' => $className])
            ->saveTo($filePath);

        if (!$saved) {
            Out::warn("Service already exists: app/Services/{$className}.php");
            return;
        }

        Out::success("Service created: app/Services/{$className}.php");
    }
}
