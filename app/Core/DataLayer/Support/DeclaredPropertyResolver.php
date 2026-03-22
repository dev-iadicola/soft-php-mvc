<?php

declare(strict_types=1);

namespace App\Core\DataLayer\Support;

use ReflectionClass;
use ReflectionProperty;

class DeclaredPropertyResolver
{
    /**
     * @param array<int, string> $excludedProperties
     * @return array<ReflectionProperty>
     */
    public static function resolve(
        ReflectionClass $reflection,
        array $excludedProperties = [],
        ?string $excludedDeclaringClass = null
    ): array {
        return array_values(array_filter(
            $reflection->getProperties(),
            static fn (ReflectionProperty $property): bool => ! $property->isStatic()
                && ! in_array($property->getName(), $excludedProperties, true)
                && ($excludedDeclaringClass === null || $property->getDeclaringClass()->getName() !== $excludedDeclaringClass)
        ));
    }
}
