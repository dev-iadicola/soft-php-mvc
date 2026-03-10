<?php

declare(strict_types=1);

namespace App\Core\DataLayer\Query;


use App\Core\Contract\QueryBuilderInterface;
use App\Core\Exception\QueryBuilderException;

abstract class AbstractBuilder implements QueryBuilderInterface
{
    // Only declared model columns should survive mass-assignment filtering.
    protected array $allowedColumns = [];
    // Binding params
    protected array $bindings = [];

    protected array $systemColumns = ['id', 'created_at', 'updated_at'];

    public int|float|string|null $id = null; // ID dell'istanza
    protected int $paramCounter = 0;

    // Cluse for QB
    protected string $from = '';
    protected string $selectValues = '*';
    protected bool $distinct = false;
    protected string $joinClause = '';
    protected string $groupByClause = '';
    protected string $havingClause = '';
    protected string $orderByClause = '';
    protected string $limitClause = '';
    protected string $offsetClause = '';
    protected ?string $table = null;
    protected string $insertClause = '';
    protected string $setClause = '';
    protected string $whereClause = '';
    protected bool $timestamps = true;

    public function timestampsExists(bool $bool): void
    {
        $this->timestamps = $bool;
        if (!$this->timestamps) {
            // When timestamps are disabled, remove created_at/updated_at from system columns.
            $this->systemColumns = array_values(array_diff(
                $this->systemColumns,
                ['created_at', 'updated_at']
            ));
        }
    }


    public function toUpdate(): string
    {
        if (empty($this->table)) {
            throw new QueryBuilderException("No table defined for UPDATE");
        }

        if (empty($this->setClause)) {
            throw new QueryBuilderException("No SET clause defined for UPDATE");
        }

        $table = str_replace('FROM ', '', $this->table);

        return trim("UPDATE $table SET {$this->setClause} {$this->whereClause};");
    }

    //───────────────────────────────────────────────────────────────
    // SETTERS AND GETTERS
    //───────────────────────────────────────────────────────────────
    #region SETTERS AND GETTERS
    /**
     * Summary of attributeExist
     * @param string $name
     * @return bool
     * Permette di verificare se un attributo esiste nell'array $attribute, molto utile per evitare errori
     * quando si accede a proprietà dinamiche.
     * Questa funzione è utilizzata nei metodi __get e __set per garantire che gli attributi siano validi prima di accedervi
     * o modificarli.
     */
    protected function attributeExist(string $name): bool
    {
        return in_array($name, $this->allowedColumns, true);
    }

    public function getAllowedColumns(): array
    {
        return $this->allowedColumns;
    }

    public function fill(array $values): array
    {
        if ($this->allowedColumns === []) {
            return $values;
        }

        // Drop keys that are not part of the model schema exposed to the query builder.
        return array_filter(
            $values,
            fn($key) => in_array($key, $this->allowedColumns, true),
            ARRAY_FILTER_USE_KEY
        );
    }


    /**
     * Summary of setTable
     * @deprecated use form($table)
     * @param string $table
     * @return void
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * Summary of setAllowedColumns
     * Utilizzata quando il query builder riceve lo schema persistibile dal model.
     * @param array $allowedColumns
     * @return void
     */
    public function setAllowedColumns(array $allowedColumns): void
    {
        // Preserve the order from the model so generated INSERT statements stay predictable.
        $this->allowedColumns = $allowedColumns;
    }

    public function setFillable(array $fillable): void
    {
        $this->setAllowedColumns($fillable);
    }

    public function setBinding(string $key, $value): string
    {
        return $this->bindings[$key] = $value;
    }

    // * ___________________________________________________
    #region GETTER
    // * ___________________________________________________

    public function getNameTable(): string|null
    {
        return $this->table;
    }


    // * ___________________________________________________
    #region BINDING
    // * ___________________________________________________
    public function getBindings(?string $key = null): array|string
    {
        return is_null($key) ? $this->bindings : $this->bindings[$key];
    }

    protected function addBind(mixed $val): mixed
    {
        $key = ":p_" . ++$this->paramCounter;
        $this->bindings[":p_" . $this->paramCounter] = $val;
        return $key;
    }



    // * ___________________________________________________
    #region GENERAL FUNCTION SQL
    // * ___________________________________________________

    // * ___________________________________________________
    #region SELECT FROM DISTCT
    // * ___________________________________________________

    /**
     * @param array<string>|string $value Columns to select.
     * @return static Fluent interface.
     */
    public function select(array|string $value): static
    {
        is_array($value)
            ? $this->selectValues = implode(', ', $value)
            : $this->selectValues = $value;
        return $this;
    }
  // * Example of use User::select(['name', 'email'])->distinct()->get();
    public function distinct(): static
    {
        $this->distinct = true;
        return $this;
    }

    public function from(string $table): static
    {
        $this->table = $table;
        $this->from = "FROM $table";
        return $this;
    }

    // * ___________________________________________________
    #region JOIN
    // * ___________________________________________________

    public function join(string $table, string $firstColumn, string $operator, string $secondColumn): static
    {
        $this->joinClause .= " INNER JOIN $table ON $firstColumn $operator $secondColumn ";
        return $this;
    }
    public function leftJoin(string $table, string $firstColumn, string $operator, string $secondColumn): static
    {
        $this->joinClause .= " LEFT JOIN $table ON $firstColumn $operator $secondColumn ";
        return $this;
    }
    public function rightJoin(string $table, string $firstColumn, string $operator, string $secondColumn): static
    {
        $this->joinClause .= " RIGHT JOIN $table ON $firstColumn $operator $secondColumn ";
        return $this;
    }

    // * ___________________________________________________
    #region WHERE FUNCTIONS
    // * ___________________________________________________

    /**
     * Summary of getPrefix
     * add in the string 'WHERE' if the clauses where is emppty or 'AND' if the clause have string.
     * @return string
     */
    protected function getPrefix(): string
    {
        return empty($this->whereClause) ? "WHERE" : "AND";
    }

    /**
     * Summary of where
     * @param string $columnName
     * @param mixed $conditionOrValueSQL operator or value if the operator is not specified
     * @param  mixed $value value of parameter for make operation
     *
     */
    public function where(string $columnName, mixed $conditionOrValue, mixed $value = null): static
    {
        if ($value === null) {
            $this->whereClause .= "{$this->getPrefix()} $columnName = {$this->addBind($conditionOrValue)} ";
        } else {
            $this->whereClause .= "{$this->getPrefix()} $columnName $conditionOrValue {$this->addBind($value)} ";
        }
        return $this;
    }
    #region NOT
    public function whereNot(string $columnName, mixed $value): static
    {

        $this->whereClause .= " {$this->getPrefix()} $columnName <> {$this->addBind($value)} ";
        return $this;
    }
    public function orWhere(string $column, mixed $conditionOrValue, mixed $value = null): static
    {
        if ($value === null) {
            $this->whereClause .= " OR  $column = {$this->addBind($conditionOrValue)}";
        } else {
            $this->whereClause .= " OR  $column $conditionOrValue {$this->addBind($value)}";
        }
        return $this;
    }
    #region NULL

    public function whereNull(string $columnName): static
    {
        $this->whereClause .= " {$this->getPrefix()} $columnName IS NULL ";
        return $this;
    }
    public function whereNotNull(string $columnName): static
    {
        $this->whereClause .= " {$this->getPrefix()} $columnName IS NOT NULL  ";
        return $this;
    }
    #region IN
    public function whereIn(string $column, array $values): static
    {
        return $this->buildWhereIn($column, $values, 'IN');
    }

    public function whereNotIn(string $column, array $values): static
    {
        return $this->buildWhereIn($column, $values, 'NOT IN');
    }

    private function buildWhereIn(string $column, array $values, string $operator): static
    {
        if (empty($values)) {
            throw new QueryBuilderException("Array empty in {$operator} clause for column {$column}");
        }

        $placeholders = [];
        foreach ($values as $value) {
            $placeholders[] = $this->addBind($value);
        }

        $inClause = implode(', ', $placeholders);
        $this->whereClause .= " {$this->getPrefix()} {$column} {$operator}({$inClause})";

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
     *
     */
    public function orderBy(array|string $columns, string $direction = 'ASC'): static
    {
        // * It was removed because it will cause problems when you use join funcions
        // if (!empty($this->orderByClause)) throw new QueryBuilderException("You can't use OrderBy() more than once in the same query for model {$this->modelClass} ");
        // * Check the columns
        // * check the allowed direction
        $allowedDirections = ['ASC', 'DESC'];
        $direction = strtoupper(trim($direction));
        if (!in_array($direction, $allowedDirections, true)) {
            throw new QueryBuilderException("Invalid direction '$direction' in orderBy()");
        }
        // Build the ORDER BY clause.
        if (is_array($columns)) {
            $orderby = implode(', ', array_map(fn($col) => "$col $direction", $columns));
        } else {
            $orderby = " $columns $direction ";
        }
        $this->orderByClause = 'ORDER BY ' . $orderby;
        return $this;
    }
 #region grp by

    /**
     *
     * @param string|array<string> $columns
     * @return static
     *
     *
     */
    public function groupBy(string|array $columns): static
    {
        // Prevent multiple calls
        if (!empty($this->groupByClause)) {
            throw new QueryBuilderException("You can't use groupBy() more than once in the same query");
        }
        $this->groupByClause = 'GROUP BY ' . (is_array($columns) ? implode(', ', $columns) : $columns);
        return $this;
    }
    // * ___________________________________________________
    #region HAVING
    // * ___________________________________________________
    public function having(string $column, string $operator, mixed $value): static
    {
        $this->havingClause .= " HAVING {$column} {$operator} {$this->addBind($value)} ";
        return $this;
    }
    #region LIMIT and OFFSET
    public function limit(int $limit): static
    {
        $this->limitClause = " LIMIT {$limit} ";
        return $this;
    }
    public function offset(int $offset): static
    {
        $this->offsetClause = " OFFSET {$offset} ";
        return $this;
    }



    // * ___________________________________________________
    #region CRUD HELPERS
    // * ___________________________________________________

    #region SET
    public function set(array $values): static
    {
        $filtered = $this->fill($values);

        if (empty($filtered)) {
            throw new QueryBuilderException("No valid fields provided for UPDATE operation.");
        }
        if (empty(trim($this->whereClause))) {
            throw new QueryBuilderException(" Invalid Update operation, where clause is empty!");
        }
        $assignments = [];
        foreach ($filtered as $column => $value) {
            $paramKey = $this->addBind($value); // :p_1, :p_2, ecc.
            $assignments[] = "{$column} = {$paramKey}";
        }

        $this->setClause = implode(', ', $assignments);

        return $this;
    }


    // * ___________________________________________________
    #region BETWEEN
    // * ___________________________________________________
    public function whereBetween(string $column, string|int|float $min, string|int|float $max): static
    {
        $this->whereClause .= " {$this->getPrefix()} {$column} BETWEEN {$this->addBind($min)} AND {$this->addBind($max)} ";
        return $this;
    }
    public function whereNotBetween(string $column, string|int|float $min, string|int|float $max): static
    {
        $this->whereClause .= " {$this->getPrefix()} {$column} NOT BETWEEN {$this->addBind($min)} AND {$this->addBind($max)} ";
        return $this;
    }



    // * ___________________________________________________
    #region To QUERY
    // * ___________________________________________________


    public function toSql(): string
    {
        $parts = [
            "SELECT " . ($this->distinct ? 'DISTINCT ' : '') . " {$this->selectValues}",
            trim($this->from),
            trim($this->joinClause),
            trim($this->whereClause),
            trim($this->groupByClause),
            trim($this->havingClause),
            trim($this->orderByClause),
            trim($this->limitClause),
            trim($this->offsetClause)
        ];

        // Remove empty parts of string and normalize tab and space
        $query = preg_replace('/\s+/', ' ', implode(' ', array_filter($parts))) . ';';

        return $query;
    }
    public function toInsert(): string
    {
        if (empty($this->setClause)) {
            throw new QueryBuilderException("No INSERT statement defined");
        }
        return $this->setClause . ';';
    }

    public function toDelete(): string
    {
        if (empty($this->table)) {
            throw new QueryBuilderException("DELETE error: no table defined.");
        }

        if (empty($this->whereClause)) {
            throw new QueryBuilderException("DELETE blocked: missing WHERE clause.");
        }

        $query = "DELETE FROM {$this->table} {$this->whereClause};";

        return $query;
    }


    // * ___________________________________________________
    #region RESET
    // * ___________________________________________________

    public function reset(): void
    {
        $this->bindings = [];
        $this->paramCounter = 0;
        $this->selectValues = '*';
        $this->distinct = false;
        $this->joinClause = '';
        $this->groupByClause = '';
        $this->havingClause = '';
        $this->orderByClause = '';
        $this->limitClause = '';
        $this->offsetClause = '';
        $this->insertClause = '';
        $this->setClause = '';
        $this->whereClause = '';
    }



}
