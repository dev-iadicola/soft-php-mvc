<?php

namespace App\Core\Contract;

use App\Core\Exception\QueryBuilderException;

interface QueryBuilderInterface extends SqlExecutableInterface
{
    // Core
    public function select(array|string $columns): static;
    public function distinct(): static;
    public function from(string $table): static;


    // WHERE
    public function where(string $column, mixed $operatorOrValue, mixed $value = null): static;
    public function whereNot(string $column, mixed $value): static;
    public function orWhere(string $column, mixed $operatorOrValue, mixed $value = null): static;
    public function whereNull(string $column, mixed $value): static;
    public function whereNotNull(string $column, mixed $value): static;
    public function whereIn(string $column, array $values): static;
    public function whereNotIn(string $column, array $values): static;
    public function whereBetween(string $column, string|int|float $min, string|int|float $max): static;
    public function whereNotBetween(string $column, string|int|float $min, string|int|float $max): static;

    // JOIN
    public function join(string $table, string $firstColumn, string $operator, string $secondColumn): static;
    public function leftJoin(string $table, string $firstColumn, string $operator, string $secondColumn): static;
    public function rightJoin(string $table, string $firstColumn, string $operator, string $secondColumn): static;

    // GROUP BY / HAVING
    public function groupBy(string|array $columns): static;
    public function having(string $column, string $operator, mixed $value): static;

    // ORDER BY
    public function orderBy(array|string $columns, string $direction = 'ASC'): static;

    // LIMIT / OFFSET
    public function limit(int $limit): static;
    public function offset(int $offset): static;

    public function insert(array $insert):static;

}
interface SqlExecutableInterface
{
    public function toSql(): string;
    public function toUpdate(): string;
    public function toInsert(): string;
    public function toDelete():string;
    public function toRawSql(): string;
    public function getBindings(): array|string;
    public function fill(array $array):array;

    public function setFillable(array $values);
}


