<?php

namespace App\Core\DataLayer\Query;

use PDO;
use App\Core\DataLayer\Model;
use App\Core\Contract\QueryBuilderInterface;
use App\Core\Exception\QueryBuilderException;
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
    ) {

    }
    #region FREE QUERY FUNCITON
    /**
     * Summary of query 
     * Prepare your query. This method is very useful when dealing with a complex query 
     * @param string $query 
     * @param array<string> $params parametri da serire per il bindValue
     * @param int $fetchType
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
        $sql = "SELECT EXISTS(" . str_replace(';', '', $this->builder->toSql()) . ")";
        $binds = $this->builder->getBindings();
        $this->builder->reset();
        return (bool) $this->executor->fetchColumn($sql, $binds);
    }

    #region SELECT
    /**
     * Summary of select
     * @param array|string $select
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
    #region FORM
    public function form(string $table): static
    {
        $this->builder->from($table);
        return $this;
    }


    #region JOIN

    public function join(string $table, string $firstColumn, string $operator, string $secondColumn): static
    {
        $this->builder->join($table, $firstColumn, $operator, $secondColumn);
        return $this;
    }
    public function leftJoin(string $table, string $firstColumn, string $operator, string $seconColumn): static
    {
        $this->builder->leftJoin($table, $firstColumn, $operator, $seconColumn);

        return $this;
    }
    public function rightJoin(string $table, string $firstColumn, string $operator, string $secondColumn): static
    {
        $this->builder->rightJoin($table, $firstColumn, $operator, $secondColumn);
        return $this;
    }

    // * ___________________________________________________
    #region WHERE 
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
    #region BETWEEN
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
    #region ORDER BY
    // * ___________________________________________________

    /**
     * Summary of orderBy
     * @param array<string>|string $columns
     * @param string $direction
     * @throws \App\Core\Exception\QueryBuilderException
     * @return ActiveQuery
     */
    public function orderBy(array|string $columns, string $direction = 'ASC'): static
    {
        $this->builder->orderBy($columns, $direction);
        return $this;
    }


    // * ___________________________________________________
    #region GRPUP BY
    // * ___________________________________________________
    /**
     * Groups the results by one or more columns.
     * @param string|array<string> $columns  One or more columns for the GROUP BY clause
     * @return static
     *
     * @throws QueryBuilderException If the columns are invalid or if the method is called multiple times
     */
    public function groupBy(string|array $columns): static
    {
        $this->builder->groupBy($columns);
        return $this;
    }

    // * ___________________________________________________
    #region HAVING LIMIT AND OFFSET
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
    #region DEBUG
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
    #region CRUD OPERATIONS
    // * ___________________________________________________

    /** GET */
    public function get(int $fetchType = PDO::FETCH_ASSOC): array
    {
        $rows = $this->executor->fetchAll($this->builder->toSql(), $this->builder->getBindings(), $fetchType);
        $this->builder->reset();
        return $this->hydrator->many($rows);
    }
    public function first(int $fetchType = PDO::FETCH_ASSOC)
    {
        $this->builder->limit(1);
        $row = $this->executor->fetch(query: $this->builder->toSql(), bindings: $this->builder->getBindings(), fetchType: $fetchType);
        $this->builder->reset();
        return $this->hydrator->one($row);
    }

    public function find(int|string $id, ?string $column = 'id', int $fetchType = PDO::FETCH_ASSOC)
    {
        $this->builder->reset();
        $this->builder->where($column, $id);
        $row = $this->executor->fetch($this->builder->toSql(), $this->builder->getBindings(), $fetchType);
        $this->builder->reset();
        return $this->hydrator->one($row);
    }
    public function findOrFalse(int|string $id, ?string $column = 'id', int $fetchType = PDO::FETCH_ASSOC): bool|Model
    {
        $this->builder->where($column, $id);

        $exist = $this->executor->fetch($this->builder->toSql(), $this->builder->getBindings());
        $this->builder->reset();
        if ($exist) {
            return $this->hydrator->one($exist);
        } else {
            return false;
        }
    }


    public function findAll(int $fetchType = PDO::FETCH_ASSOC): array
    {
        $rows = $this->executor->fetchAll($this->builder->tosql(), $this->builder->getBindings(), $fetchType);
        $this->builder->reset();
        return $this->hydrator->many($rows);
    }

    /** CREATE */
    /**
     * Summary of create
     * @param array $values
     * @return Model|null
     */
    public function create(array $values): Model
    {
        $this->builder->insert($values);
        $this->executor->prepareAndExecute($this->builder->toInsert(), $this->builder->getBindings());
        $id = $this->executor->lastInsertId();

        return $this->find($id);
    }

    public function update(array $values): bool
    {
        $this->builder->set($values);

        $result = $this->executor->prepareAndExecute(
            $this->builder->toUpdate(),
            $this->builder->getBindings()
        );

        $this->builder->reset();

        // prepareAndExecute restituisce PDOStatement
        // consideriamo "true" se non ci sono eccezioni
        return $result !== false;
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
