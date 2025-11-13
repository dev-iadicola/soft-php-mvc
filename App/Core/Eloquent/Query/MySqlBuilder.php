<?php

namespace App\Core\Eloquent\Query;

use PDO;
use App\Core\Eloquent\Query\AbstractBuilder;
use App\Core\Exception\QueryBuilderException;


class MySqlBuilder extends AbstractBuilder
{
    private string $selectValues = '*'; // Campi da selezionare
    private bool $distinct = false;
    private string $joinClause = '';
    private string $groupByClause = ''; // Clausola GROUP BY
    private string $havingClause = '';
    private string $orderByClause = ''; // Clausola ORDER BY
    private string $limitClause = ''; // Clausola Limit
    private string $offsetClause = '';


    public function __construct()
    {

    }



    // * ___________________________________________________
    #region TO STRING 
    // * ___________________________________________________



    /**
     * Summary of toUpdate
     * return a stirng with query for update record
     * @return string
     */
    public function toUpdate(): string
    {
        return "UPDATE {$this->table} SET {$this->setClause} WHERE {$this->whereClause}";
    }

    public function toInsert(): string
    {
        return $this->insertClause;
    }


    public function toSql(): string
    {
        $parts = [
            "SELECT " . ($this->distinct ? 'DISTINCT ' : '') . " {$this->selectValues}",
            trim($this->table),
            trim($this->joinClause),
            trim($this->whereClause),
            trim($this->groupByClause),
            trim($this->havingClause),
            trim($this->orderByClause),
            trim($this->limitClause),
            trim($this->offsetClause)
        ];
        // Remove empty parts of string and normalize tab and space
        return preg_replace('/\s+/', ' ', implode(' ', array_filter($parts))) . ';';
    }

    public function from(string $table): static
    {
        $this->table = "FROM $table";
        return $this;
    }
    public function toRawSql(): string
    {
        $sql = $this->toSql();
        foreach ($this->bindings as $key => $val) {
            $val = is_numeric($val) ? $val : "'$val'";
            $sql = str_replace($key, $val, $sql);
        }
        return $sql;
    }





    // * ___________________________________________________
    #region SELECT 
    // * ___________________________________________________

    /**
     * @param array<string>|string $value Elenco dei campi da selezionare.
     * @return static Ritorna l’istanza corrente per permettere chiamate fluide.
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
     * @return QueryBuilder 
     */
    public function where(string $columnName, mixed $conditionOrValue, mixed $value = null): static
    {
        if ($value === null) {
            $this->whereClause .= "{$this->getPrefix()} $columnName = {$this->AddBind($conditionOrValue)} ";
        } else {
            $this->whereClause .= "{$this->getPrefix()} $columnName $conditionOrValue {$this->AddBind($value)} ";
        }
        return $this;
    }
    public function whereNot(string $columnName, mixed $value): static
    {

        $this->whereClause .= " {$this->getPrefix()} $columnName <> {$this->AddBind($value)} ";
        return $this;
    }
    public function orWhere(string $column, mixed $conditionOrValue, mixed $value = null): static
    {
        if ($value = null) {
            $this->whereClause .= " OR WHERE $column = {$this->addBind($conditionOrValue)}";
        } else {
            $this->whereClause .= " OR WHERE $column $conditionOrValue {$this->addBind($value)}";
        }
        return $this;
    }
    public function whereNull(string $columnName, mixed $value): static
    {
        $this->whereClause .= " {$this->getPrefix()} $columnName IS NULL {$this->AddBind($value)} ";
        return $this;
    }
    public function whereNotNull(string $columnName, mixed $value): static
    {
        $this->whereClause .= " {$this->getPrefix()} $columnName IS NOT NULL {$this->AddBind($value)} ";
        return $this;
    }
    public function whereIn(string $column, array $values): static
    {
        if (empty($value)) {
            throw new QueryBuilderException("Array empty in whereIn() method for column $column ");
        }
        $placeholder = [];
        foreach ($values as $value) {
            $placeholder[] = $this->AddBind($value); // reutnr key p_$count
        }
        $inCluase = implode(', ', $placeholder);
        $this->whereClause .= " {$this->getPrefix()} IN($inCluase)";
        return $this;
    }
    public function whereNotIn(string $column, array $values): static
    {
        if (empty($value)) {
            throw new QueryBuilderException("Array empty in whereIn() method for column $column ");
        }
        $placeholder = [];
        foreach ($values as $value) {
            $placeholder[] = $this->AddBind($value); // reutnr key p_$count
        }
        $inCluase = implode(', ', $placeholder);
        $this->whereClause .= " {$this->getPrefix()} NOT IN($inCluase)";
        return $this;
    }
    // * ___________________________________________________
    #region BETWEEN
    // * ___________________________________________________
    public function whereBetween(string $column, string|int|float $min, string|int|float $max): static
    {
        $this->whereClause .= " {$this->getPrefix()} {$column} BETWEEN {$this->AddBind($min)} AND {$this->AddBind($max)} ";
        return $this;
    }
    public function whereNotBetween(string $column, string|int|float $min, string|int|float $max): static
    {
        $this->whereClause .= " {$this->getPrefix()} {$column} NOT BETWEEN {$this->AddBind($min)} AND {$this->AddBind($max)} ";
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
        // * It was removed because it will cause problems when you use join funcions
        // if (!empty($this->orderByClause)) throw new QueryBuilderException("You can't use OrderBy() more than once in the same query for model {$this->modelClass} ");
        // * Check the columns 
        // * check the allowed direction
        $allowedDirections = ['ASC', 'DESC'];
        $direction = strtoupper(trim($direction));
        if (!in_array($direction, $allowedDirections, true)) {
            throw new QueryBuilderException("Invalid direction '$direction' in orderBy()");
        }
        // costruzione della clausola orderBy.
        if (is_array($columns)) {
            $orderby = implode(', ', array_map(fn($col) => "$col $direction", $columns));
        } else {
            $orderby = " $columns $direction ";
        }
        $this->orderByClause = 'ORDER BY ' . $orderby;
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
        // Evita l’uso multiplo
        if (!empty($this->groupByClause)) {
            throw new QueryBuilderException("You can't use groupBy() more than once in the same query for model {$this->modelClass}");
        }
        // Validazione colonne con context automatico
        // Costruisce la clausola SQL
        $this->groupByClause = 'GROUP BY ' . (is_array($columns) ? implode(', ', $columns) : $columns);
        return $this;
    }

    // * ___________________________________________________
    #region HAVING 
    // * ___________________________________________________
    public function having(string $column, string $operator, mixed $value): static
    {
        $this->havingClause .= " HAVING {$column} {$operator} {$this->AddBind($value)} ";
        return $this;
    }
    #region LIMIT

    public function limit(int $limit): static
    {
        $this->limitClause = " LIMIT {$limit} ";
        return $this;
    }

    #region OFFSET

    public function offset(int $offset): static
    {
        $this->offsetClause = " OFFSET {$offset} ";
        return $this;
    }

    #region INSERT

    public function insert(array $values): static
    {
        $filteredValues = $this->fill($values);
        if (empty($filteredValues)) {
            throw new \InvalidArgumentException("Impossible enter any value.");
        }
        $this->bindings = [];
        $columns = [];
        $placeholders = [];
        if (property_exists($this, 'timestamps') && $this->timestamps === true) {
            $now = date('Y-m-d H:i:s');
            $filteredValues['created_at'] = $filteredValues['created_at'] ?? $now;
            $filteredValues['updated_at'] = $filteredValues['updated_at'] ?? $now;
        }

        // preprare binding
        foreach ($filteredValues as $column => $val) {
            // add safe column name
            $columns[] = $column;
            $placeholders[] = $this->AddBind($val);
        }


        // prepare columns and placeholder for Query
        $this->insertClause = "INSERT INTO {$this->table} " . implode(', ', $columns) . " VALUES " . implode(', ', $placeholders);
        return $this;
    }

    #region SET
    // TODO: not conluded, to do finished process for method update in ORM eng
    public function set(array $values): static
    {
        $filtered = $this->fill($values);

        if (empty($filtered)) {
            throw new QueryBuilderException("No valid fields provided for UPDATE operation.");
        }
        $assignments = [];
        foreach ($filtered as $column => $value) {
            $paramKey = $this->AddBind($value); // :p_1, :p_2, ecc.
            $assignments[] = "{$column} = {$paramKey}";
        }

        $this->setClause = implode(', ', $assignments);

        return $this;
    }

}
