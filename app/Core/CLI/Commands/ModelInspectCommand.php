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
            Out::error('You must specify a model name. Example: php soft model:inspect Article');
            return;
        }

        try {
            $className = Str::studly($name);
            $fqcn = 'App\\Model\\' . $className;

            if (!class_exists($fqcn)) {
                Out::error("Model class not found: {$fqcn}");
                return;
            }

            $this->inspect($fqcn, $className);
        } catch (\Throwable $e) {
            Out::error("Failed to inspect model: {$e->getMessage()}");
        }
    }

    private function inspect(string $fqcn, string $className): void
    {
        $reflection = new ReflectionClass($fqcn);
        $instance = new $fqcn();

        $table = $instance->getTable();

        Out::ln("──────────────────────────────────────────────");
        Out::info("Model: {$className}");
        Out::info("Table: {$table}");
        Out::ln("──────────────────────────────────────────────");

        $properties = $this->getDataProperties($reflection);

        if (empty($properties)) {
            Out::warn("No declared data properties found on {$className}.");
            return;
        }

        // Header
        $header = sprintf(
            "  %-20s %-25s %-10s %-15s",
            'Property',
            'Type',
            'Nullable',
            'Default'
        );
        Out::ln($header);
        Out::ln("  " . str_repeat('-', 70));

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $type = $this->resolveType($property);
            $nullable = $this->isNullable($property) ? 'yes' : 'no';
            $default = $this->resolveDefault($property, $instance);

            $row = sprintf(
                "  %-20s %-25s %-10s %-15s",
                $propertyName,
                $type,
                $nullable,
                $default
            );
            Out::ln($row);
        }

        Out::ln("──────────────────────────────────────────────");
        Out::info("Total properties: " . count($properties));
    }

    /**
     * Get declared data properties (excluding internal framework properties).
     *
     * @return array<ReflectionProperty>
     */
    private function getDataProperties(ReflectionClass $reflection): array
    {
        return DeclaredPropertyResolver::resolve(
            $reflection,
            self::INTERNAL_PROPERTIES,
            'App\\Core\\DataLayer\\Model'
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

    private function resolveDefault(ReflectionProperty $property, object $instance): string
    {
        if ($property->hasDefaultValue()) {
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

        return '(none)';
    }
}
