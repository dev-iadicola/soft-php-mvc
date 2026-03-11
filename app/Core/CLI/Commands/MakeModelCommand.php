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

        $php = $command[0] ?? 'php';

        $generators = $this->buildGenerators($className, $table, $php);

        foreach ($generators as $key => $generator) {
            if ($options[$key] ?? false) {
                $generator();
            }
        }
    }

    /**
     * @return array<string, callable(): void>
     */
    private function buildGenerators(string $className, string $table, string $php): array
    {
        return [
            'migration' => fn () => (new MakeMigrationCommand())->exe([
                $php, 'make:migration', 'create_' . $table . '_table',
            ]),
            'resource' => fn () => (new MakeControllerCommand())->exe([
                $php, 'make:controller', $className . 'Controller',
            ]),
            'repository' => fn () => (new MakeRepositoryCommand())->createRepository(
                $className . 'Repository', $className,
            ),
            'service' => fn () => (new MakeServiceCommand())->createService(
                $className . 'Service',
            ),
        ];
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
            'repository' => false,
            'service' => false,
            'table' => null,
        ];

        foreach ($args as $arg) {
            if ($arg === '--migration') {
                $options['migration'] = true;
                continue;
            }

            if ($arg === '--resource' || $arg === '--controller') {
                $options['resource'] = true;
                continue;
            }

            if ($arg === '--repository') {
                $options['repository'] = true;
                continue;
            }

            if ($arg === '--service') {
                $options['service'] = true;
                continue;
            }

            if (str_starts_with($arg, '--table=')) {
                $options['table'] = trim(substr($arg, 8));
                continue;
            }

            // Short flags: -m (migration), -c (controller), -r (repository), -s (service)
            // Combinable: -mcrs
            if (str_starts_with($arg, '-') && !str_starts_with($arg, '--')) {
                $flags = substr($arg, 1);
                for ($i = 0, $len = strlen($flags); $i < $len; $i++) {
                    match ($flags[$i]) {
                        'm' => $options['migration'] = true,
                        'c' => $options['resource'] = true,
                        'r' => $options['repository'] = true,
                        's' => $options['service'] = true,
                        default => Out::warn("Unknown flag: -{$flags[$i]}"),
                    };
                }
            }
        }

        return $options;
    }

}
