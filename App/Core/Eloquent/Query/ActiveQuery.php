<?php

namespace App\Core\Eloquent\Query;

use PDO;
use App\Core\Eloquent\Model;
use App\Core\Eloquent\OrmEngine;
use App\Core\Contract\QueryBuilderInterface;
use App\Core\Exception\QueryBuilderException;
use App\Core\Exception\ModelNotFoundException;
use App\Core\Exception\ModelStructureException;
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

    /**
     * Summary of query 
     * Prepara la tua query. Questo metodo è molto utile se si tratta di una query complessa. 
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
        return $this->hydrator->many($data);
    }

    public function exists(): bool
    {
        $sql = "SELECT EXISTS(" . $this->builder->toSql() . ")";
        return (bool) $this->executor->fetchColumn($sql, $this->builder->getBindings());
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
    public function whereNull(string $columnName, mixed $value): static
    {
        $this->builder->whereNull($columnName, $value);
        return $this;
    }
    public function whereNotNull(string $columnName, mixed $value): static
    {
        $this->builder->whereNotNull($columnName, $value);
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
        $this->whereNotBetween($column, $min, $max);
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
     * @return QueryBuilder
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
     * Raggruppa i risultati per una o più colonne.
     * @param string|array<string> $columns  Una o più colonne per la clausola GROUP BY
     * @return static
     *
     * @throws QueryBuilderException Se le colonne non sono valide o se il metodo viene richiamato più volte
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
        return $this->hydrator->many($rows);
    }
    public function first(int $fetchType = PDO::FETCH_ASSOC)
    {
        $this->builder->limit(1);
        $row = $this->executor->fetch(query: $this->builder->toSql(), bindings: $this->builder->getBindings());
        return $this->hydrator->one($row);
    }

    public function find(int|string $id, ?string $column = 'id', int $fetchType = PDO::FETCH_ASSOC)
    {
        $this->builder->where($column, $id);
        $row = $this->executor->fetch($this->builder->toSql(), $this->builder->getBindings());
        return $this->hydrator->one($row);
    }
    public function findOrFail(int|string $id, ?string $column = 'id', int $fetchType = PDO::FETCH_ASSOC)
    {
        $this->builder->where($column, $id);
        return $this->executor->fetch($this->builder->toSql(), $this->builder->getBindings(), $fetchType) ?? throw new ModelNotFoundException("Id {$id} Not Found in Model {$this->hydrator}");
    }
    public function findAll(int $fetchType = PDO::FETCH_ASSOC): array
    {
        $rows = $this->executor->fetchAll($this->builder->tosql(), $this->builder->getBindings());
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
        $stmt = $this->executor->prepareAndExecute($this->builder->toInsert(), $this->builder->getBindings());
        $stmt->execute();
        $id = $this->executor->lastInsertId();

        return $this->find($id);
    }
    
   /** DELETE */
    public function delete(): bool|PDOStatement
    {
        if (empty($this->table)) {
            throw new \Exception("Empty name of table, set protected \$table in your {$this->hydrator}");
        }

        if (empty($this->whereClause)) {
            if (!isset($this->id)) {
                throw new QueryBuilderException('No condition was selected in the delete action. For security reasons, it is not possible to delete all records in a table.');
            }
        }        

       return $this->executor->prepareAndExecute($this->builder->toDelete(), $this->builder->getBindings());
    }




}
