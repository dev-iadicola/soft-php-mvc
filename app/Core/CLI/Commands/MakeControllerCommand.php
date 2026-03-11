<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\CLI\Commands\Validation\ValidateClassName;
use App\Core\CLI\Stubs\StubGenerator;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\Helpers\Str;

class MakeControllerCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        $name = $command[2] ?? null;

        if (!$name || str_starts_with($name, '-')) {
            Out::error('You must specify a controller name. Example: php soft make:controller UserController');
            return;
        }

        try {
            $className = Str::studly($name);
            if (!str_ends_with($className, 'Controller')) {
                $className .= 'Controller';
            }

            ValidateClassName::Validate($className, 'Controller');

            $filePath = getcwd() . '/app/Controllers/' . $className . '.php';

            $saved = StubGenerator::make('controller')
                ->replace(['{{CLASS}}' => $className])
                ->saveTo($filePath);

            if (!$saved) {
                Out::warn("Controller already exists: app/Controllers/{$className}.php");
                return;
            }

            Out::success("Controller created: app/Controllers/{$className}.php");
        } catch (\InvalidArgumentException $e) {
            Out::error($e->getMessage());
        } catch (\Throwable $e) {
            Out::error("Failed to create controller: {$e->getMessage()}");
        }
    }
}
