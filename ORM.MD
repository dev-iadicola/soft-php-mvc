# ORM Layer

This ORM uses typed model properties as the canonical schema for persistence.
The framework no longer relies on `fillable`.

## Architecture

The runtime is split into focused components:

- `Model`: table metadata, typed properties, casting, dirty tracking
- `ActiveQuery`: fluent query API plus create/update/save coordination
- `QueryBuilder`: SQL generation and binding management
- `QueryExecutor`: prepared statement execution through PDO
- `ModelHydrator`: row-to-model hydration

## Typed Models

Each model declares its persisted columns as typed properties.
Framework properties such as `$table`, `$primaryKey`, `$timestamps`, `$attributes`, `$original`, and `$dirty` are excluded automatically.

```php
<?php

namespace App\Model;

use App\Core\DataLayer\Model;

class Article extends Model
{
    protected string $table = 'articles';
    protected int|string|null $id = null;
    protected ?string $title = null;
    protected ?string $subtitle = null;
    protected ?string $created_at = null;
}
```

`columns(): array` is the canonical API for discovered DB columns.
`getPersistableColumns()` remains as a compatibility alias.

## Column Mapping

When a PHP property cannot safely match the DB column name, override `columnMap()`.

```php
protected function columnMap(): array
{
    return ['migrationTable' => 'migration_table'];
}
```

Hydration and serialization accept both the PHP property name and the mapped DB column name.

## Casting

Models may define casts for typed assignment and hydration.

```php
protected function casts(): array
{
    return [
        'selected' => 'bool',
        'download' => 'int',
        'meta' => 'json',
    ];
}
```

Supported casts:

- `bool` / `boolean`
- `int` / `integer`
- `float` / `double`
- `string`
- `array` / `json`
- `date`
- `datetime`

## Persistence Semantics

`save()` now uses dirty tracking:

- new models insert only changed attributes
- existing models update only dirty attributes
- unchanged persisted models do not issue an update

Hydrated models call `syncOriginal()` automatically, so a freshly loaded model starts clean.

Useful APIs:

- `isDirty(?string $key = null): bool`
- `getDirtyAttributes(): array`
- `getAttributesForInsert(): array`
- `getAttributesForUpdate(): array`
- `syncOriginal(): static`

## Query Usage

```php
$articles = Article::query()
    ->where('title', '!=', '')
    ->orderBy('created_at', 'DESC')
    ->get();

$article = new Article();
$article->setAttribute('title', 'Typed ORM');
$article->save();
```

## Notes

- Unknown columns still fall back to the dynamic `$attributes` bag.
- Nullable properties should be declared nullable in PHP when the DB column allows `NULL`.
- If the database provides defaults, avoid touching the property unless you want to override that default during insert.
