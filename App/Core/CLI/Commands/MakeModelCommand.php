<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

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
        $className = $this->normalizeClassName($name);
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
        $modelDir = getcwd() . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Model';
        $filePath = $modelDir . DIRECTORY_SEPARATOR . $className . '.php';

        if (!is_dir($modelDir)) {
            mkdir($modelDir, 0755, true);
        }

        if (file_exists($filePath)) {
            Out::warn("Model already exists: App/Model/{$className}.php");
            return;
        }

        $stubPath = __DIR__ . '/../Stubs/model.stub';
        $stub = file_get_contents($stubPath);

        if ($stub === false) {
            Out::error('Model stub not found.');
            return;
        }

        $content = str_replace(
            ['{{CLASS}}', '{{TABLE}}'],
            [$className, $table],
            $stub
        );

        file_put_contents($filePath, $content);

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

    private function normalizeClassName(string $name): string
    {
        $segments = preg_split('/[^A-Za-z0-9]+/', $name) ?: [];
        $segments = array_filter($segments, static fn (string $segment): bool => $segment !== '');

        return implode('', array_map(static fn (string $segment): string => ucfirst($segment), $segments));
    }
}
