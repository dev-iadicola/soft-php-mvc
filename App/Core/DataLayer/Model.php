<?php

declare(strict_types=1);

namespace App\Core\DataLayer;

use App\Core\DataLayer\Factory\ActiveQueryFactory;
use App\Core\Helpers\Str;
use JsonSerializable;
use App\Core\Traits\Attributes;
use App\Core\DataLayer\Query\ActiveQuery;
use DateTimeInterface;
use ReflectionClass;
use ReflectionProperty;


class Model  implements JsonSerializable
{
    use Attributes;

    // These framework-level properties must never be treated as database columns.
    public const DATE_FORMAT = 'Y-m-d';
    public const DATETIME_FORMAT = 'Y-m-d H:i:s';

    private const INTERNAL_PROPERTIES = [
        'primaryKey',
        'table',
        'timestamps',
        'attributes',
        'original',
        'dirty',
    ];

    private static array $declaredPropertiesCache = [];

    public string $primaryKey = 'id';
    protected string $table = '';
    protected bool $timestamps = true;
    protected array $original = [];
    protected array $dirty = [];

    public static function instance(): static{
        return new static;
    }

    public function getTable(): string
    {
        if ($this->table !== '') {
            return $this->table;
        }

        // Auto-resolve: App\Model\Article → "articles", App\Model\Technology → "technologies"
        $class = (new ReflectionClass(static::class))->getShortName();
        return $this->table = Str::plural(Str::lower($class));
    }

    public function getKeyId(): string
    {
        return $this->primaryKey;
    }

    public function jsonSerialize(): mixed
    {
        return $this->getAttribute();
    }

    public function setAttribute(string $key, mixed $value): void
    {
        // Hydration first tries declared typed properties, then falls back to dynamic attributes.
        $property = $this->resolvePropertyName($key);

        if ($property !== null) {
            $castedValue = $this->castAttribute($property, $value);
            $this->$property = $castedValue;
            $this->markDirty($property, $castedValue);
            return;
        }

        $castedValue = $this->castDynamicAttribute($key, $value);
        $this->attributes[$key] = $castedValue;
        $this->markDirty($key, $castedValue);
    }

    public function getAttribute(?string $key = null): mixed{
        if (is_null($key)) {
            return $this->toArray();
        }

        $property = $this->resolvePropertyName($key);

        if ($property !== null) {
            return $this->$property;
        }

        return $this->attributes[$key] ?? null;
    }

    protected function setTimestamps(bool $bool): bool
    {
        return $this->timestamps = $bool;
    }

    protected function setTable(string $table): string
    {
        return $this->table = $table;
    }

    public function save(): void
    {
         $this->query()->save($this);
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->getDeclaredDataProperties() as $property) {
            $name = $property->getName();
            // Models can expose a different database column name than the PHP property name.
            $data[$this->getColumnName($name)] = $this->$name;
        }

        return array_merge($data, $this->attributes);
    }

    public function getPersistableColumns(): array
    {
        return $this->columns();
    }

    public function columns(): array
    {
        // The query builder uses this list to filter INSERT and UPDATE payloads.
        return array_map(
            fn (ReflectionProperty $property): string => $this->getColumnName($property->getName()),
            $this->getDeclaredDataProperties()
        );
    }

    public function isDirty(?string $key = null): bool
    {
        if ($key === null) {
            return $this->dirty !== [];
        }

        $resolvedKey = $this->resolvePropertyName($key) ?? $key;

        return array_key_exists($resolvedKey, $this->dirty);
    }

    public function getDirtyAttributes(): array
    {
        $dirty = [];

        foreach ($this->dirty as $key => $value) {
            $dirty[$this->getOutputColumnName($key)] = $value;
        }

        return $dirty;
    }

    public function syncOriginal(): static
    {
        $this->original = [];

        foreach ($this->getDeclaredDataProperties() as $property) {
            $name = $property->getName();
            $this->original[$name] = $this->$name;
        }

        foreach ($this->attributes as $key => $value) {
            $this->original[$key] = $value;
        }

        $this->dirty = [];

        return $this;
    }

    public function exists(): bool
    {
        $primaryKey = $this->resolvePropertyName($this->primaryKey) ?? $this->primaryKey;

        return $this->getStoredValue($primaryKey) !== null;
    }

    public function getAttributesForInsert(): array
    {
        return $this->getDirtyAttributes();
    }

    public function getAttributesForUpdate(): array
    {
        $dirty = $this->getDirtyAttributes();
        unset($dirty[$this->primaryKey]);

        return $dirty;
    }

    public function __toString(): string
    {
        return self::class;
    }

    public static function query(): ActiveQuery
    {
        return ActiveQueryFactory::for(static::class);
    }

    /**
     * @return array<ReflectionProperty>
     */
    private function getDeclaredDataProperties(): array
    {
        // Cache reflection results per model class to avoid recomputing schema metadata.
        return self::$declaredPropertiesCache[static::class] ??= array_values(array_filter(
            (new ReflectionClass(static::class))->getProperties(),
            fn (ReflectionProperty $property): bool => ! $property->isStatic()
                && ! in_array($property->getName(), self::INTERNAL_PROPERTIES, true)
                && $property->getDeclaringClass()->getName() !== self::class
        ));
    }

    private function isDeclaredModelProperty(string $key): bool
    {
        return $this->resolvePropertyName($key) !== null;
    }

    protected function columnMap(): array
    {
        // Override in child models when a PHP property cannot match the DB column name.
        return [];
    }

    protected function casts(): array
    {
        return [];
    }

    private function getColumnName(string $property): string
    {
        return $this->columnMap()[$property] ?? $property;
    }

    private function getOutputColumnName(string $key): string
    {
        return $this->isDataPropertyName($key) ? $this->getColumnName($key) : $key;
    }

    private function resolvePropertyName(string $key): ?string
    {
        // Accept both the PHP property name and the mapped database column name.
        if (property_exists($this, $key) && $this->isDataPropertyName($key)) {
            return $key;
        }

        $property = array_search($key, $this->columnMap(), true);
        if (is_string($property) && $this->isDataPropertyName($property)) {
            return $property;
        }

        return null;
    }

    private function isDataPropertyName(string $key): bool
    {
        foreach ($this->getDeclaredDataProperties() as $property) {
            if ($property->getName() === $key) {
                return true;
            }
        }

        return false;
    }

    private function markDirty(string $key, mixed $value): void
    {
        $original = $this->original[$key] ?? null;

        if (array_key_exists($key, $this->original) && $original === $value) {
            unset($this->dirty[$key]);
            return;
        }

        $this->dirty[$key] = $value;
    }

    private function getStoredValue(string $key): mixed
    {
        if ($this->isDataPropertyName($key)) {
            return $this->$key;
        }

        return $this->attributes[$key] ?? null;
    }

    private function castDynamicAttribute(string $key, mixed $value): mixed
    {
        $property = array_search($key, $this->columnMap(), true);
        if (is_string($property)) {
            return $this->castAttribute($property, $value);
        }

        return $value;
    }

    private function castAttribute(string $property, mixed $value): mixed
    {
        $cast = $this->casts()[$property] ?? $this->casts()[$this->getColumnName($property)] ?? null;

        if ($cast === null || $value === null) {
            return $value;
        }

        return match ($cast) {
            'bool', 'boolean' => (bool) $value,
            'int', 'integer' => (int) $value,
            'float', 'double' => (float) $value,
            'string' => (string) $value,
            'array', 'json' => is_string($value) ? json_decode($value, true) ?? [] : (array) $value,
            'date' => $value instanceof DateTimeInterface ? $value->format(self::DATE_FORMAT) : (string) $value,
            'datetime' => $value instanceof DateTimeInterface ? $value->format(self::DATETIME_FORMAT) : (string) $value,
            default => $value,
        };
    }
}
