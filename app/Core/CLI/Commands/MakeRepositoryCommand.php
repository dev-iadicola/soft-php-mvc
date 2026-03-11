<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\CLI\Commands\Validation\ValidateClassName;
use App\Core\CLI\Stubs\StubGenerator;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\Helpers\Str;

class MakeRepositoryCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        $name = $command[2] ?? null;

        if (!$name || str_starts_with($name, '-')) {
            Out::error('You must specify a repository name. Example: php soft make:repository UserRepository');
            return;
        }

        try {
            $className = Str::studly($name);

            if (!str_ends_with($className, 'Repository')) {
                $className .= 'Repository';
            }

            ValidateClassName::Validate($className, 'Repository');

            $modelName = str_replace('Repository', '', $className);

            $this->createRepository($className, $modelName);
        } catch (\InvalidArgumentException $e) {
            Out::error($e->getMessage());
        } catch (\Throwable $e) {
            Out::error("Failed to create repository: {$e->getMessage()}");
        }
    }

    public function createRepository(string $className, string $modelName): void
    {
        $filePath = getcwd() . '/app/Repository/' . $className . '.php';

        $saved = StubGenerator::make('repository')
            ->replace(['{{CLASS}}' => $className, '{{MODEL}}' => $modelName])
            ->saveTo($filePath);

        if (!$saved) {
            Out::warn("Repository already exists: app/Repository/{$className}.php");
            return;
        }

        Out::success("Repository created: app/Repository/{$className}.php");
    }
}
