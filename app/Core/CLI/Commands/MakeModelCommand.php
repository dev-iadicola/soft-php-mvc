<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\CLI\Stubs\StubGenerator;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\Helpers\Str;

class MakeModelCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        $name = $command[2] ?? null;

        if (!$name || str_starts_with($name, '--')) {
            Out::error('You must specify a model name. Example: php soft make:model Post');
            return;
        }

        $options = $this->parseOptions(array_slice($command, 3));
        $className = Str::studly($name);
        $table = $options['table'] ?? Str::plural(Str::lower($className));

        $this->createModel($className, $table);

        if ($options['migration']) {
            (new MakeMigrationCommand())->exe([
                $command[0] ?? 'php',
                'make:migration',
                'create_' . $table . '_table',
            ]);
        }

        if ($options['resource']) {
            (new MakeControllerCommand())->exe([
                $command[0] ?? 'php',
                'make:controller',
                $className . 'Controller',
            ]);
        }
    }

    private function createModel(string $className, string $table): void
    {
        $filePath = getcwd() . '/App/Model/' . $className . '.php';

        $saved = StubGenerator::make('model')
            ->replace(['{{CLASS}}' => $className, '{{TABLE}}' => $table])
            ->saveTo($filePath);

        if (!$saved) {
            Out::warn("Model already exists: App/Model/{$className}.php");
            return;
        }

        Out::success("Model created: App/Model/{$className}.php");
    }

    private function parseOptions(array $args): array
    {
        $options = [
            'migration' => false,
            'resource' => false,
            'table' => null,
        ];

        foreach ($args as $arg) {
            if ($arg === '--migration') {
                $options['migration'] = true;
                continue;
            }

            if ($arg === '--resource') {
                $options['resource'] = true;
                continue;
            }

            if (str_starts_with($arg, '--table=')) {
                $options['table'] = trim(substr($arg, 8));
            }
        }

        return $options;
    }

}
