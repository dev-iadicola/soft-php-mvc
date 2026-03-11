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

            $options = $this->parseOptions(array_slice($command, 3));

            ValidateClassName::Validate($className, 'Controller');

            $filePath = getcwd() . '/app/Controllers/' . $className . '.php';

            $prefix = $this->resolvePrefix($options['prefix']);
            $middlewares = $this->resolveMiddlewares($options['middleware']);
            [$useStatements, $attributes] = $this->buildAttributeBlock($prefix, $middlewares);

            $saved = StubGenerator::make('controller')
                ->replace([
                    '{{CLASS}}' => $className,
                    '{{USES}}' => $useStatements,
                    '{{ATTRIBUTES}}' => $attributes,
                ])
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

    /**
     * @param array<int, string> $args
     * @return array{prefix: ?string, middleware: ?string}
     */
    private function parseOptions(array $args): array
    {
        $options = [
            'prefix' => null,
            'middleware' => null,
        ];

        foreach ($args as $arg) {
            if (str_starts_with($arg, '--prefix=')) {
                $options['prefix'] = substr($arg, strlen('--prefix='));
                continue;
            }

            if (str_starts_with($arg, '--middleware=')) {
                $options['middleware'] = substr($arg, strlen('--middleware='));
            }
        }

        return $options;
    }

    private function resolvePrefix(?string $prefix): ?string
    {
        if ($prefix !== null) {
            return $this->normalizePrefix($prefix);
        }

        if (!$this->isInteractive()) {
            return null;
        }

        $input = $this->ask('Controller prefix (leave empty for none): ');
        return $this->normalizePrefix($input);
    }

    /**
     * @return list<string>
     */
    private function resolveMiddlewares(?string $middlewareOption): array
    {
        if ($middlewareOption !== null) {
            return $this->normalizeMiddlewares($middlewareOption);
        }

        if (!$this->isInteractive()) {
            return [];
        }

        $input = $this->ask('Controller middleware (comma separated, leave empty for none): ');
        return $this->normalizeMiddlewares($input);
    }

    /**
     * @param list<string> $middlewares
     * @return array{0: string, 1: string}
     */
    private function buildAttributeBlock(?string $prefix, array $middlewares): array
    {
        $uses = [];
        $attributes = [];

        if ($prefix !== null) {
            $uses[] = 'use App\Core\Http\Attributes\Prefix;';
            $attributes[] = "#[Prefix('{$prefix}')]";
        }

        if ($middlewares !== []) {
            $uses[] = 'use App\Core\Http\Attributes\Middleware;';
            $middlewareLiteral = count($middlewares) === 1
                ? "'{$middlewares[0]}'"
                : '[' . implode(', ', array_map(fn(string $middleware): string => "'{$middleware}'", $middlewares)) . ']';

            $attributes[] = "#[Middleware({$middlewareLiteral})]";
        }

        $useStatements = $uses === [] ? '' : "\n" . implode("\n", $uses);
        $attributeBlock = $attributes === [] ? '' : implode("\n", $attributes) . "\n";

        return [$useStatements, $attributeBlock];
    }

    private function normalizePrefix(?string $prefix): ?string
    {
        if ($prefix === null) {
            return null;
        }

        $prefix = trim($prefix);
        if ($prefix === '') {
            return null;
        }

        return '/' . ltrim($prefix, '/');
    }

    /**
     * @return list<string>
     */
    private function normalizeMiddlewares(?string $middlewares): array
    {
        if ($middlewares === null) {
            return [];
        }

        $parts = array_map(
            static fn(string $middleware): string => trim($middleware),
            explode(',', $middlewares)
        );

        $parts = array_values(array_filter($parts, static fn(string $middleware): bool => $middleware !== ''));

        return array_values(array_unique($parts));
    }

    private function isInteractive(): bool
    {
        if (!defined('STDIN')) {
            return false;
        }

        if (function_exists('stream_isatty')) {
            return stream_isatty(STDIN);
        }

        return function_exists('posix_isatty') && posix_isatty(STDIN);
    }

    private function ask(string $prompt): string
    {
        if (function_exists('readline')) {
            $value = readline($prompt);
            return is_string($value) ? $value : '';
        }

        Out::ln($prompt);

        $value = fgets(STDIN);

        return is_string($value) ? trim($value) : '';
    }
}
