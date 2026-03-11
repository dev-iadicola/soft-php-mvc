<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\CLI\Commands\Validation\ValidateClassName;
use App\Core\CLI\Stubs\StubGenerator;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\Helpers\Str;

class MakeMiddlewareCommand implements CommandInterface
{
    public function exe(array $params): void
    {
        $name = $params[2] ?? null;

        if (!$name || str_starts_with($name, '-')) {
            Out::error('You must specify a middleware name. Example: php soft make:mw AuthMiddleware');
            return;
        }

        $this->createMiddleware($name);
    }

    public function createMiddleware(string $name): void
    {
        try {
            $className = Str::studly($name);
            if (!str_ends_with($className, 'Middleware')) {
                $className .= 'Middleware';
            }

            ValidateClassName::Validate($className, 'Middleware');

            $filePath = getcwd() . '/app/Middleware/' . $className . '.php';

            $saved = StubGenerator::make('middleware')
                ->replace(['{{CLASS}}' => $className])
                ->saveTo($filePath);

            if (!$saved) {
                Out::warn("Middleware already exists: app/Middleware/{$className}.php");
                return;
            }

            Out::success("Middleware created: app/Middleware/{$className}.php");
            Out::ln("Configure your middleware in config/middleware.php");
        } catch (\InvalidArgumentException $e) {
            Out::error($e->getMessage());
        } catch (\Throwable $e) {
            Out::error("Failed to create middleware: {$e->getMessage()}");
        }
    }
}
