<?php

namespace App\Core\Traits;

use App\Core\DataLayer\Model;
use App\Core\DataLayer\Query\ActiveQuery;
use App\Core\DataLayer\Factory\ActiveQueryFactory;

trait StaticQueryMethods
{
    public static function Make(): ActiveQuery{
        return ActiveQueryFactory::for(static::class);
    }

    
    // -----------------------------------------------------
    // BASIC QUERY SHORTCUTS
    // -----------------------------------------------------

    public static function select(array|string $columns = ['*']): ActiveQuery
    {
        return static::Make()->select($columns);
    }

    public static function where(string $column, mixed $operatorOrValue, mixed $value = null): ActiveQuery
    {
        return static::Make()->where($column, $operatorOrValue, $value);
    }

    public static function orWhere(string $column, mixed $operatorOrValue, mixed $value = null): ActiveQuery
    {
        return static::Make()->orWhere($column, $operatorOrValue, $value);
    }

    public static function whereIn(string $column, array $values): ActiveQuery
    {
        return static::Make()->whereIn($column, $values);
    }

    public static function whereNotIn(string $column, array $values): ActiveQuery
    {
        return static::Make()->whereNotIn($column, $values);
    }

    public static function whereNull(string $column): ActiveQuery
    {
        return static::Make()->whereNull(columnName: $column);
    }

    public static function whereNotNull(string $column): ActiveQuery
    {
        return static::Make()->whereNotNull(columnName: $column);
    }


    // -----------------------------------------------------
    // GROUPING
    // -----------------------------------------------------

    public static function groupBy(string|array $columns): ActiveQuery
    {
        return static::Make()->groupBy($columns);
    }


    // -----------------------------------------------------
    // ORDERING
    // -----------------------------------------------------

    public static function orderBy(string|array $column, string $direction = 'ASC'): ActiveQuery
    {
        return static::Make()->orderBy($column, $direction);
    }


    // -----------------------------------------------------
    // LIMIT / PAGINATION
    // -----------------------------------------------------

    public static function limit(int $limit): ActiveQuery
    {
        return static::Make()->limit($limit);
    }

    public static function offset(int $offset): ActiveQuery
    {
        return static::Make()->offset($offset);
    }


    // -----------------------------------------------------
    // TODO: RELATIONS (future) 
    // -----------------------------------------------------

    // public static function with(array|string $relations): ActiveQuery
    // {
    //     return static::Make()->with($relations);
    // }


    // -----------------------------------------------------
    // FETCH SHORTCUTS
    // -----------------------------------------------------

    /**
     * Summary of get
     * deprecated
     * @return array
     */
    public static function get(): array
    {
        return static::Make()->get();
    }

    public static function first(): ?Model
    {
        return static::Make()->first();
    }

    public static function find(int|string $id): ?Model
    {
        return static::where('id', $id)->first();
    }

    public static function findOrFail(int|string $id): Model
    {
        $result = static::find($id);

        return $result;
    }

    public static function all(): array
    {
        return static::get();
    }
    /**
     * Summary of findAll
     * @deprecated use find()
     */
    public static function findAll(): array
    {
        return static::get();
    }


    // -----------------------------------------------------
    // AGGREGATE FUNCTIONS
    // -----------------------------------------------------

    public static function count(string $column = '*'): int
    {
        return static::Make()->count($column);
    }

    public static function max(string $column): int|float|null
    {
        return static::Make()->max($column);
    }

    public static function min(string $column): int|float|null
    {
        return static::Make()->min($column);
    }

    public static function sum(string $column): int|float|null
    {
        return static::Make()->sum($column);
    }

    public static function avg(string $column): int|float|null
    {
        return static::Make()->avg($column);
    }

}
