<?php

declare(strict_types=1);

namespace App\Core\DataLayer\Query;

use App\Core\Contract\QueryBuilderInterface;
use App\Core\DataLayer\Model;
use App\Core\Exception\ModelException;
use App\Core\Exception\QueryBuilderException;
use PDO;
use PDOStatement;

/**
 * Summary of ActiveQuery
 * Work of Unit: with QueryBuilder, Execute and Actions
 */
class ActiveQuery
{
    public function __construct(
        private QueryBuilderInterface $builder,
        private QueryExecutor $executor,
        private ModelHydrator $hydrator,
    ) {}

    // region FREE QUERY FUNCITON
    /**
     * Summary of query
     * Prepare your query. This method is very useful when dealing with a complex query
     *
     * @param  array<string>  $params  parametri da serire per il bindValue
     * @return array<Model>
     */
    public function query(string $query, ?array $params = [], int $fetchType = PDO::FETCH_ASSOC): array
    {
        $stmt = $this->executor->prepare($query);
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        $stmt->execute();
        $data = $stmt->fetchAll($fetchType);
        $this->builder->reset();

        return $this->hydrator->many($data);
    }

    public function exists(): bool
    {
        $sql = 'SELECT EXISTS(' . str_replace(';', '', $this->builder->toSql()) . ')';
        $binds = $this->builder->getBindings();
        $this->builder->reset();

        return (bool) $this->executor->fetchColumn($sql, $binds);
    }

    public function find(int|string $id): ?Model
    {
        $pk = $this->hydrator->getModel()->primaryKey;
        return $this->where($pk, $id)->first();
    }

    public function all(): array
    {
        return $this->get();
    }

    public function findOrFail(int|string $id): Model
    {
        $result = $this->find($id);
        if (!$result) {
            throw new ModelException("Model not found for ID: {$id}");
        }
        return $result;
    }

    // region SELECT
    /**
     * Summary of select
     *
     * @return ActiveQuery
     */
    public function select(array|string $select): static
    {
        $this->builder->select($select);

        return $this;
    }

    public function distinct(): static
    {
        $this->builder->distinct();

        return $this;
    }

    // region FROM
    public function from(string $table): static
    {
        $this->builder->from($table);

        return $this;
    }

    // region JOIN

    public function join(string $table, string $firstColumn, string $operator, string $secondColumn): static
    {
        $this->builder->join($table, $firstColumn, $operator, $secondColumn);

        return $this;
    }

    public function leftJoin(string $table, string $firstColumn, string $operator, string $secondColumn): static
    {
        $this->builder->leftJoin($table, $firstColumn, $operator, $secondColumn);

        return $this;
    }

    public function rightJoin(string $table, string $firstColumn, string $operator, string $secondColumn): static
    {
        $this->builder->rightJoin($table, $firstColumn, $operator, $secondColumn);

        return $this;
    }

    // * ___________________________________________________
    // region WHERE
    // * ___________________________________________________
    public function where(string $columnName, mixed $conditionOrValue, mixed $value = null): static
    {
        $this->builder->where($columnName, $conditionOrValue, $value);

        return $this;
    }

    public function whereNot(string $columnName, mixed $value): static
    {
        $this->builder->whereNot($columnName, $value);

        return $this;
    }

    public function orWhere(string $column, mixed $conditionOrValue, mixed $value = null): static
    {
        $this->builder->orWhere($column, $conditionOrValue, $value);

        return $this;
    }

    public function whereNull(string $columnName): static
    {
        $this->builder->whereNull($columnName);

        return $this;
    }

    public function whereNotNull(string $columnName): static
    {
        $this->builder->whereNotNull($columnName);

        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        $this->builder->whereIn($column, $values);

        return $this;
    }

    public function whereNotIn(string $column, array $values): static
    {
        $this->builder->whereNotIn($column, $values);

        return $this;
    }

    // * ___________________________________________________
    // region BETWEEN
    // * ___________________________________________________
    public function whereBetween(string $column, string|int|float $min, string|int|float $max): static
    {
        $this->builder->whereBetween($column, $min, $max);

        return $this;
    }

    public function whereNotBetween(string $column, string|int|float $min, string|int|float $max): static
    {
        $this->builder->whereNotBetween($column, $min, $max);

        return $this;
    }

    // * ___________________________________________________
    // region ORDER BY
    // * ___________________________________________________

    /**
     * Summary of orderBy
     *
     * @param  array<string>|string  $columns
     * @return ActiveQuery
     *
     * @throws QueryBuilderException
     */
    public function orderBy(array|string $columns, string $direction = 'ASC'): static
    {
        $this->builder->orderBy($columns, $direction);

        return $this;
    }

    // * ___________________________________________________
    // region GRPUP BY
    // * ___________________________________________________
    /**
     * Groups the results by one or more columns.
     *
     * @param  string|array<string>  $columns  One or more columns for the GROUP BY clause
     *
     * @throws QueryBuilderException If the columns are invalid or if the method is called multiple times
     */
    public function groupBy(string|array $columns): static
    {
        $this->builder->groupBy($columns);

        return $this;
    }

    // * ___________________________________________________
    // region HAVING LIMIT AND OFFSET
    // * ___________________________________________________
    public function having(string $column, string $operator, mixed $value): static
    {
        $this->builder->having($column, $operator, $value);

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->builder->limit($limit);

        return $this;
    }

    public function offset(int $offset): static
    {
        $this->builder->offset($offset);

        return $this;
    }


    // * ___________________________________________________
    // region AGGREGATE
    // * ___________________________________________________

    /**
     * Add a raw SQL expression to the SELECT clause.
     */
    public function selectRaw(string $expression): static
    {
        $this->builder->selectRaw($expression);
        return $this;
    }

    /**
     * Add an aggregate function to the SELECT clause.
     */
    public function selectAggregate(string $function, string $column, ?string $alias = null): static
    {
        $this->builder->selectAggregate($function, $column, $alias);
        return $this;
    }

    /**
     * GROUP BY with a raw expression.
     */
    public function groupByRaw(string $expression): static
    {
        $this->builder->groupByRaw($expression);
        return $this;
    }

    /**
     * HAVING with a raw expression.
     */
    public function havingRaw(string $expression): static
    {
        $this->builder->havingRaw($expression);
        return $this;
    }

    /**
     * Return COUNT(*) or COUNT(column) as a single integer.
     */
    public function count(string $column = '*'): int
    {
        $sql = $this->builder->toAggregate('COUNT', $column);
        $bindings = $this->builder->getBindings();
        $this->builder->reset();

        return (int) $this->executor->fetchColumn($sql, $bindings);
    }

    /**
     * Return COUNT(DISTINCT column) as a single integer.
     */
    public function countDistinct(string $column): int
    {
        $sql = $this->builder->toAggregate('COUNT', "DISTINCT {$column}");
        $bindings = $this->builder->getBindings();
        $this->builder->reset();

        return (int) $this->executor->fetchColumn($sql, $bindings);
    }

    /**
     * Return SUM(column) as a numeric value.
     */
    public function sum(string $column): int|float
    {
        $sql = $this->builder->toAggregate('SUM', $column);
        $bindings = $this->builder->getBindings();
        $this->builder->reset();

        $result = $this->executor->fetchColumn($sql, $bindings);
        return $result === false ? 0 : (int) $result;
    }

    /**
     * Return AVG(column) as a float.
     */
    public function avg(string $column): float
    {
        $sql = $this->builder->toAggregate('AVG', $column);
        $bindings = $this->builder->getBindings();
        $this->builder->reset();

        $result = $this->executor->fetchColumn($sql, $bindings);
        return $result === false ? 0.0 : (float) $result;
    }

    /**
     * Return MAX(column).
     */
    public function max(string $column): mixed
    {
        $sql = $this->builder->toAggregate('MAX', $column);
        $bindings = $this->builder->getBindings();
        $this->builder->reset();

        $result = $this->executor->fetchColumn($sql, $bindings);
        return $result === false ? null : $result;
    }

    /**
     * Return MIN(column).
     */
    public function min(string $column): mixed
    {
        $sql = $this->builder->toAggregate('MIN', $column);
        $bindings = $this->builder->getBindings();
        $this->builder->reset();

        $result = $this->executor->fetchColumn($sql, $bindings);
        return $result === false ? null : $result;
    }

    /**
     * Execute the current query and return raw associative arrays (not hydrated models).
     * Useful for aggregate queries with GROUP BY that don't map to a single model.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchRows(int $fetchType = PDO::FETCH_ASSOC): array
    {
        $rows = $this->executor->fetchAll($this->builder->toSql(), $this->builder->getBindings(), $fetchType);
        $this->builder->reset();

        return is_array($rows) ? $rows : [];
    }

    /**
     * Execute the current query and return a single scalar value (first column, first row).
     */
    public function scalar(): mixed
    {
        $sql = $this->builder->toSql();
        $bindings = $this->builder->getBindings();
        $this->builder->reset();

        return $this->executor->fetchColumn($sql, $bindings);
    }

    // * ___________________________________________________
    // region DEBUG
    // * ___________________________________________________
    public function toSql(): string
    {
        return $this->builder->toSql();
    }

    public function toRawSql(): string
    {
        return $this->builder->toRawSql();
    }

    // * ___________________________________________________
    // region CRUD OPERATIONS
    // * ___________________________________________________

    /** GET */
    public function get(int $fetchType = PDO::FETCH_ASSOC): array
    {
        $rows = $this->executor->fetchAll($this->builder->toSql(), $this->builder->getBindings(), $fetchType);
        $this->builder->reset();

        return $this->hydrator->many($rows);
    }

    public function first(int $fetchType = PDO::FETCH_ASSOC, bool $reset = false): Model|null
    {
        $this->builder->limit(1);
        $row = $this->executor->fetch(query: $this->builder->toSql(), bindings: $this->builder->getBindings(), fetchType: $fetchType);
        return $this->hydrator->one($row);
    }

    public function findAll(int $fetchType = PDO::FETCH_ASSOC): array
    {
        $rows = $this->executor->fetchAll($this->builder->toSql(), $this->builder->getBindings(), $fetchType);
        $this->builder->reset();

        return $this->hydrator->many($rows);
    }

    /** CREATE */
    /**
     * Summary of create
     *
     * @return Model|null
     */
    public function create(array $values): Model
    {
        try {
            $this->builder->insert($values);
            $this->executor->prepareAndExecute($this->builder->toInsert(), $this->builder->getBindings());
            $primaryKey = $this->hydrator->getModel()->primaryKey;
            $id = $this->executor->lastInsertId();
            $lookupId = $values[$primaryKey] ?? null;

            if ($lookupId === null && $id !== false && $id !== '0' && $id !== 0) {
                $lookupId = $id;
            }
            $this->builder->reset();

            if ($lookupId === null) {
                throw new ModelException("Unable to resolve primary key after create for model {$this->hydrator->getModel()}");
            }

            return $this->find($lookupId);
        } catch (ModelException $e) {
            throw new ModelException($e->getMessage() . ' for Model ' . $this->hydrator->getModel(), 0, $e);
        }

    }

    /**
     * Update data in database
     *
     * @param array<string, mixed> $values
     *
     * @throws ModelException
     */
    public function update(array $values): bool
    {
        try {
            $this->builder->set($values);

            $result = $this->executor->prepareAndExecute(
                $this->builder->toUpdate(),
                $this->builder->getBindings()
            );

            $this->builder->reset();

            return $result !== false;
        } catch (ModelException $e) {
            throw new ModelException($e->getMessage() . ' for Model ' . $this->hydrator->getModel(), 0, $e);
        }

    }

    public function save(Model $model): Model
    {
        if ($model->exists()) {
            $dirty = $model->getAttributesForUpdate();

            if ($dirty === []) {
                return $model;
            }

            $this->where($model->primaryKey, $model->getAttribute($model->primaryKey));
            $this->update($dirty);
            $model->syncOriginal();
            return $model;
        }

        $payload = $model->getAttributesForInsert();

        if ($payload === []) {
            return $model;
        }

        $created = $this->create($payload);

        foreach ($created->toArray() as $key => $value) {
            $model->setAttribute($key, $value);
        }

        $model->syncOriginal();
        return $model;
    }

    /** DELETE */
    public function delete(): bool|PDOStatement
    {
        // * method ToDelete has in here reset method for reset the proprieties builder
        // + the method ToDelete make check the table  and the where clausesis is isset, else is run exception
        $result = $this->executor->prepareAndExecute($this->builder->toDelete(), $this->builder->getBindings());
        $this->builder->reset();

        return $result;
    }
}
