<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\DataLayer\Support\DeclaredPropertyResolver;
use App\Core\Helpers\Str;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

class ModelInspectCommand implements CommandInterface
{
    private const MODEL_NAMESPACE = 'App\\Model\\';
    private const MODEL_DIR = 'app/Model';
    private const BASE_MODEL_CLASS = 'App\\Core\\DataLayer\\Model';

    private const INTERNAL_PROPERTIES = [
        'primaryKey',
        'table',
        'timestamps',
        'attributes',
        'original',
        'dirty',
    ];

    public function exe(array $command): void
    {
        $name = $command[2] ?? null;

        if (!$name || str_starts_with($name, '-')) {
            $this->listModels();
            return;
        }

        try {
            $className = Str::studly($name);
            $fqcn = self::MODEL_NAMESPACE . $className;

            if (!class_exists($fqcn)) {
                Out::error("Model class not found: {$fqcn}");
                return;
            }

            $this->inspect($fqcn, $className);
        } catch (\Throwable $e) {
            Out::error("Failed to inspect model: {$e->getMessage()}");
        }
    }

    private function listModels(): void
    {
        $modelDir = getcwd() . DIRECTORY_SEPARATOR . self::MODEL_DIR;

        if (!is_dir($modelDir)) {
            Out::warn("Model directory not found: {$modelDir}");
            return;
        }

        $files = glob($modelDir . DIRECTORY_SEPARATOR . '*.php');

        if (empty($files)) {
            Out::warn('No models found in ' . self::MODEL_DIR);
            return;
        }

        Out::ln("──────────────────────────────────────────────");
        Out::info("Available models (" . self::MODEL_DIR . ")");
        Out::ln("──────────────────────────────────────────────");

        $header = sprintf("  %-20s %-30s %-10s", 'Model', 'Table', 'Properties');
        Out::ln($header);
        Out::ln("  " . str_repeat('-', 60));

        foreach ($files as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            $fqcn = self::MODEL_NAMESPACE . $className;

            if (!class_exists($fqcn)) {
                continue;
            }

            $reflection = new ReflectionClass($fqcn);

            if ($reflection->isAbstract() || !$reflection->isSubclassOf(self::BASE_MODEL_CLASS)) {
                continue;
            }

            $instance = new $fqcn();
            $table = $instance->getTable();
            $properties = $this->getDataProperties($reflection);
            $count = count($properties);

            Out::ln(sprintf("  %-20s %-30s %-10s", $className, $table, $count));
        }

        Out::ln("──────────────────────────────────────────────");
        Out::ln("\nUsage: php soft model:inspect <ModelName>");
    }

    private function inspect(string $fqcn, string $className): void
    {
        $reflection = new ReflectionClass($fqcn);
        $instance = new $fqcn();

        $table = $instance->getTable();
        $casts = $this->resolveCasts($reflection, $instance);
        $columnMap = $this->resolveColumnMap($reflection, $instance);

        Out::ln("──────────────────────────────────────────────");
        Out::info("Model:        {$className}");
        Out::info("Table:        {$table}");
        Out::info("Primary key:  {$instance->primaryKey}");
        Out::ln("──────────────────────────────────────────────");

        $properties = $this->getDataProperties($reflection);

        if (empty($properties)) {
            Out::warn("No declared data properties found on {$className}.");
            return;
        }

        $header = sprintf(
            "  %-20s %-20s %-10s %-12s %-10s %-15s",
            'Property',
            'Type',
            'Nullable',
            'Default',
            'Cast',
            'Column'
        );
        Out::ln($header);
        Out::ln("  " . str_repeat('-', 87));

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $type = $this->resolveType($property);
            $nullable = $this->isNullable($property) ? 'yes' : 'no';
            $default = $this->resolveDefault($property);
            $cast = $casts[$propertyName] ?? '-';
            $column = $columnMap[$propertyName] ?? $propertyName;

            $row = sprintf(
                "  %-20s %-20s %-10s %-12s %-10s %-15s",
                $propertyName,
                $type,
                $nullable,
                $default,
                $cast,
                $column
            );
            Out::ln($row);
        }

        Out::ln("──────────────────────────────────────────────");
        Out::info("Total properties: " . count($properties));
    }

    /**
     * @return array<ReflectionProperty>
     */
    private function getDataProperties(ReflectionClass $reflection): array
    {
        return DeclaredPropertyResolver::resolve(
            $reflection,
            self::INTERNAL_PROPERTIES,
            self::BASE_MODEL_CLASS
        );
    }

    private function resolveType(ReflectionProperty $property): string
    {
        $type = $property->getType();

        if ($type === null) {
            return 'mixed';
        }

        if ($type instanceof ReflectionNamedType) {
            $prefix = $type->allowsNull() && $type->getName() !== 'mixed' ? '?' : '';
            return $prefix . $type->getName();
        }

        if ($type instanceof ReflectionUnionType) {
            $types = array_map(
                fn ($t): string => $t instanceof ReflectionNamedType ? $t->getName() : (string) $t,
                $type->getTypes()
            );
            return implode('|', $types);
        }

        return (string) $type;
    }

    private function isNullable(ReflectionProperty $property): bool
    {
        $type = $property->getType();

        if ($type === null) {
            return true;
        }

        if ($type instanceof ReflectionNamedType) {
            return $type->allowsNull();
        }

        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $t) {
                if ($t instanceof ReflectionNamedType && $t->getName() === 'null') {
                    return true;
                }
            }
        }

        return false;
    }

    private function resolveDefault(ReflectionProperty $property): string
    {
        if (!$property->hasDefaultValue()) {
            return '(none)';
        }

        $default = $property->getDefaultValue();

        if ($default === null) {
            return 'null';
        }

        if (is_bool($default)) {
            return $default ? 'true' : 'false';
        }

        if (is_array($default)) {
            return '[]';
        }

        return (string) $default;
    }

    /**
     * @return array<string, string>
     */
    private function resolveCasts(ReflectionClass $reflection, object $instance): array
    {
        $method = $reflection->getMethod('casts');
        $method->setAccessible(true);

        return $method->invoke($instance);
    }

    /**
     * @return array<string, string>
     */
    private function resolveColumnMap(ReflectionClass $reflection, object $instance): array
    {
        $method = $reflection->getMethod('columnMap');
        $method->setAccessible(true);

        return $method->invoke($instance);
    }
}
