<?php

namespace App\Core\Eloquent\Query;

use App\Core\Contract\QueryBuilderInterface;
use App\Core\Eloquent\Query\AbstractBuilder;
use App\Core\Exception\QueryBuilderException;


/**
 * ! TESTING
 * PostgreSQL implementation of the QueryBuilder.
 * 
 *  Fully compatible with OrmEngine and Model
 *  Uses double quotes for identifiers
 *  Supports ILIKE instead of LIKE
 *  Follows SQL-92 standard for joins and conditions
 */
class PostgresQueryBuilder extends AbstractBuilder
{
    protected string $selectValues = '*';
    protected bool $distinct = false;
    protected string $fromClause = '';
    protected string $joinClause = '';
    protected string $whereClause = '';
    protected string $groupByClause = '';
    protected string $havingClause = '';
    protected string $orderByClause = '';
    protected string $limitClause = '';
    protected string $offsetClause = '';
    protected string $setClause = '';

    #region SELECT
    public function select(array|string $columns): static
    {
        if (is_array($columns)) {
            $this->selectValues = implode(', ', array_map(fn($c) => "\"$c\"", $columns));
        } else {
            $this->selectValues = "\"$columns\"";
        }
        return $this;
    }
    #region DISTINCT

    public function distinct(): static
    {
        $this->distinct = true;
        return $this;
    }

    #region FROM

    public function from(string $table): static
    {
        $this->fromClause = "FROM \"$table\"";
        return $this;
    }

    #region JOIN
    public function join(string $table, string $first, string $operator, string $second): static
    {
        $this->joinClause .= " INNER JOIN \"$table\" ON \"$first\" $operator \"$second\"";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): static
    {
        $this->joinClause .= " LEFT JOIN \"$table\" ON \"$first\" $operator \"$second\"";
        return $this;
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): static
    {
        $this->joinClause .= " RIGHT JOIN \"$table\" ON \"$first\" $operator \"$second\"";
        return $this;
    }

    #region WHERE FUNCTIONS
    protected function prefixWhere(): string
    {
        return empty($this->whereClause) ? "WHERE" : "AND";
    }

    public function where(string $column, mixed $operatorOrValue, mixed $value = null): static
    {
        if ($value === null) {
            $this->whereClause .= " {$this->prefixWhere()} \"$column\" = {$this->addBind($operatorOrValue)}";
        } else {
            $this->whereClause .= " {$this->prefixWhere()} \"$column\" $operatorOrValue {$this->addBind($value)}";
        }
        return $this;
    }

    public function whereNot(string $column, mixed $value): static
    {
        $this->whereClause .= " {$this->prefixWhere()} \"$column\" <> {$this->addBind($value)}";
        return $this;
    }

    public function orWhere(string $column, mixed $operatorOrValue, mixed $value = null): static
    {
        if ($value === null) {
            $this->whereClause .= " OR \"$column\" = {$this->addBind($operatorOrValue)}";
        } else {
            $this->whereClause .= " OR \"$column\" $operatorOrValue {$this->addBind($value)}";
        }
        return $this;
    }

    public function whereNull(string $column, mixed $value = null): static
    {
        $this->whereClause .= " {$this->prefixWhere()} \"$column\" IS NULL";
        return $this;
    }

    public function whereNotNull(string $column, mixed $value = null): static
    {
        $this->whereClause .= " {$this->prefixWhere()} \"$column\" IS NOT NULL";
        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        if (empty($values)) {
            throw new QueryBuilderException("Empty array passed to whereIn() for column {$column}");
        }
        $placeholders = implode(', ', array_map(fn($v) => $this->addBind($v), $values));
        $this->whereClause .= " {$this->prefixWhere()} \"$column\" IN ($placeholders)";
        return $this;
    }

    public function whereNotIn(string $column, array $values): static
    {
        if (empty($values)) {
            throw new QueryBuilderException("Empty array passed to whereNotIn() for column {$column}");
        }
        $placeholders = implode(', ', array_map(fn($v) => $this->addBind($v), $values));
        $this->whereClause .= " {$this->prefixWhere()} \"$column\" NOT IN ($placeholders)";
        return $this;
    }

    public function whereBetween(string $column, string|int|float $min, string|int|float $max): static
    {
        $this->whereClause .= " {$this->prefixWhere()} \"$column\" BETWEEN {$this->addBind($min)} AND {$this->addBind($max)}";
        return $this;
    }

    public function whereNotBetween(string $column, string|int|float $min, string|int|float $max): static
    {
        $this->whereClause .= " {$this->prefixWhere()} \"$column\" NOT BETWEEN {$this->addBind($min)} AND {$this->addBind($max)}";
        return $this;
    }
    #region OIRDER BY

    public function orderBy(array|string $columns, string $direction = 'ASC'): static
    {
        $allowedDirections = ['ASC', 'DESC'];
        $direction = strtoupper(trim($direction));

        if (!in_array($direction, $allowedDirections, true)) {
            throw new QueryBuilderException("Invalid order direction '$direction'");
        }

        $columns = is_array($columns) ? $columns : [$columns];
        $cols = implode(', ', array_map(fn($c) => "\"$c\" $direction", $columns));

        if (!empty($this->orderByClause)) {
            $this->orderByClause .= ', ' . $cols;
        } else {
            $this->orderByClause = "ORDER BY " . $cols;
        }

        return $this;
    }

    #region GROUP BY
    public function groupBy(string|array $columns): static
    {
        $columns = is_array($columns) ? $columns : [$columns];
        $cols = implode(', ', array_map(fn($c) => "\"$c\"", $columns));

        if (!empty($this->groupByClause)) {
            $this->groupByClause .= ', ' . $cols;
        } else {
            $this->groupByClause = "GROUP BY " . $cols;
        }

        return $this;
    }

    #region HAVING

    public function having(string $column, string $operator, mixed $value): static
    {
        $this->havingClause .= " HAVING \"$column\" $operator {$this->addBind($value)}";
        return $this;
    }
    #region LIMIT

    public function limit(int $limit): static
    {
        $this->limitClause = " LIMIT $limit";
        return $this;
    }

    #region OFFSET

    public function offset(int $offset): static
    {
        $this->offsetClause = " OFFSET $offset";
        return $this;
    }

    #region TO STRING
    public function toSql(): string
    {
        $sql = trim(
            implode(' ', array_filter([
                "SELECT" . ($this->distinct ? ' DISTINCT' : '') . " {$this->selectValues}",
                $this->fromClause,
                $this->joinClause,
                $this->whereClause,
                $this->groupByClause,
                $this->havingClause,
                $this->orderByClause,
                $this->limitClause,
                $this->offsetClause
            ]))
        );

        return $sql . ';';
    }

    public function toRawSql(): string
    {
        $sql = $this->toSql();
        foreach ($this->bindings as $key => $value) {
            $v = is_numeric($value) ? $value : "'" . addslashes($value) . "'";
            $sql = str_replace($key, $v, $sql);
        }
        return $sql;
    }


    public function insert(array $insert): static
    {
        if (is_null($this->fromClause)) {
            throw new QueryBuilderException("No table defined for INSERT");
        }

        if (empty($insert)) {
            throw new QueryBuilderException("Empty insert data");
        }

        $table = $this->table;
        $columns = array_keys($insert);
        $values = array_values($insert);

        $cols = implode(', ', array_map(fn($c) => "\"$c\"", $columns));
        $placeholders = implode(', ', array_map(fn($v) => $this->addBind($v), $values));

        $this->insertClause = "INSERT INTO $table ($cols) VALUES ($placeholders)";
        return $this;
    }






}
