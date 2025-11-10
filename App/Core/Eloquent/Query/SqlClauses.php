<?php

namespace App\Core\Eloquent\Query;

use PDO;
use App\Core\Eloquent\QueryBuilder;
use App\Core\Exception\QueryBuilderException;

trait SqlClauses
{
    protected string $selectValues = '*'; // Campi da selezionare
    protected bool $distinct = false;
    protected string $joinClause = '';
    protected string $whereClause = ''; // Clausola WHERE
    protected string $groupByClause = ''; // Clausola GROUP BY
    protected string $havingClause = '';
    protected string $orderByClause = ''; // Clausola ORDER BY
    protected string $limitClause = ''; // Clausola Limit
    protected string $offsetClause = '';
    // * ___________________________________________________
    #region TO STRING 
    // * ___________________________________________________
    public function toSql(): string
    {
        $parts = [
            "SELECT " . ($this->distinct ? 'DISTINCT ' : '') . " {$this->selectValues}",
            "FROM {$this->table}",
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
    #region BINDING     
    // * ___________________________________________________
    protected function AddBind(?string $val): mixed
    {
        $val = $this->processingNull($val);
        $key = ":p_" . ++$this->paramCounter;
        $this->bindings[":p_" . $this->paramCounter] = $val;
        return $key;
    }
    private function processingNull($value): mixed
    {
        if (is_null($value))
            return " IS NULL ";
        else
            return $value;
    }

    // * ___________________________________________________
    #region SELECT 
    // * ___________________________________________________

    /**
     * Imposta i campi da selezionare nella query SQL.
     * Può ricevere una stringa singola o un array di colonne:
     * Se non viene specificato alcun campo, la query di default selezionerà `*`.
     * @param array<string>|string $value Elenco dei campi da selezionare.
     * @return self Ritorna l’istanza corrente per permettere chiamate fluide.
     */
    public function select(array|string $value): self
    {
        is_array($value)
            ? $this->selectValues = implode(', ', $value)
            : $this->selectValues = $value;
        return $this;
    }


    // * Example of use User::select(['name', 'email'])->distinct()->get();
    public function distinct(): self
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
     * @param string|int|float|bool|null $conditionOrValueSQL operator or value if the operator is not specified
     * @param  string|int|float|bool|null $value value of parameter for make operation
     * @return QueryBuilder 
     */
    public function where(string $columnName, string|int|float|bool|null $conditionOrValue, string|int|float|bool|null $value = null): self
    {
        if ($value === null) {
            $this->whereClause .= "{$this->getPrefix()} $columnName = {$this->AddBind($conditionOrValue)} ";
        } else {
            $this->whereClause .= "{$this->getPrefix()} $columnName $conditionOrValue {$this->AddBind($value)} ";
        }
        return $this;
    }
    public function whereNot(string $columnName, string|int|float|bool|null $value): self
    {

        $this->whereClause .= " {$this->getPrefix()} $columnName <> {$this->AddBind($value)} ";
        return $this;
    }
    public function orWhere(string $column, string|int|float|bool|null $conditionOrValue, string|int|float|bool|null $value = null)
    {
        if ($value = null) {
            $this->whereClause .= " OR WHERE $column = {$this->addBind($conditionOrValue)}";
        } else {
            $this->whereClause .= " OR WHERE $column $conditionOrValue {$this->addBind($value)}";
        }
        return $this;
    }
    public function whereNull(string $columnName, $value): self
    {
        $this->whereClause .= " {$this->getPrefix()} $columnName IS NULL {$this->AddBind($value)} ";
        return $this;
    }
    public function whereNotNull(string $columnName, $value): self
    {
        $this->whereClause .= " {$this->getPrefix()} $columnName IS NOT NULL {$this->AddBind($value)} ";
        return $this;
    }
    public function whereIn(string $column, array $values)
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
    public function whereNotIn(string $column, array $values)
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
    public function whereBetween(string $column, string|int|float $min, string|int|float $max): self
    {
        $this->whereClause .= " {$this->getPrefix()} {$column} BETWEEN {$this->AddBind($min)} AND {$this->AddBind($max)} ";
        return $this;
    }
    public function whereNotBetween(string $column, string|int|float $min, string|int|float $max): self
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
    public function orderBy(array|string $columns, string $direction = 'ASC'): self
    {
        // * It was removed because it will cause problems when you use join funcions
        // if (!empty($this->orderByClause)) throw new QueryBuilderException("You can't use OrderBy() more than once in the same query for model {$this->modelClass} ");
        // * Check the columns 
        $validated = $this->validateColumns($columns, true);
        // * check the allowed direction
        $allowedDirections = ['ASC', 'DESC'];
        $direction = strtoupper(trim($direction));
        // è possibile solo che ci sia ASC o DESC e nient'altro
        if (!in_array($direction, $allowedDirections, true)) {
            throw new QueryBuilderException("Invalid direction '$direction' in orderBy()");
        }
        // costruzione della clausola orderBy.
        $this->orderByClause = 'ORDER BY ' . implode(', ', array_map(fn($col) => "$col $direction", $validated));
        return $this;
    }


    // * ___________________________________________________
    #region GRPUP BY
    // * ___________________________________________________
    /**
     * Raggruppa i risultati per una o più colonne.
     * @param string|array<string> $columns  Una o più colonne per la clausola GROUP BY
     * @return self
     *
     * @throws QueryBuilderException Se le colonne non sono valide o se il metodo viene richiamato più volte
     */
    public function groupBy(string|array $columns): self
    {
        // Evita l’uso multiplo
        if (!empty($this->groupByClause)) {
            throw new QueryBuilderException("You can't use groupBy() more than once in the same query for model {$this->modelClass}");
        }
        // Validazione colonne con context automatico
        $validated = $this->validateColumns($columns, true);
        // Costruisce la clausola SQL
        $this->groupByClause = 'GROUP BY ' . implode(', ', $validated);
        return $this;
    }

    // * ___________________________________________________
    #region HAVING LIMIT AND OFFSET
    // * ___________________________________________________
    public function having(string $column, string $operator, mixed $value): self
    {
        $this->havingClause .= " HAVING {$column} {$operator} {$this->AddBind($value)} ";
        return $this;
    }
    public function limit(int $limit): self
    {
        $this->limitClause = " LIMIT {$limit} ";
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offsetClause = " OFFSET {$offset} ";
        return $this;
    }


    // * ___________________________________________________
    #region QUERY
    // * ___________________________________________________
    /**
     * Summary of query 
     * Prepara la tua query. Questo metodo è molto utile se si tratta di una query complessa. 
     * @param string $query 
     * @param array<string> $params parametri da serire per il bindValue
     * @param int $fetchType
     * @return QueryBuilder[]
     */
    public function query(string $query, ?array $params = [], int $fetchType = PDO::FETCH_ASSOC): array
    {
        $stmt = $this->pdo->prepare($query);
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        $stmt->execute();
        $data = $stmt->fetchAll($fetchType);
        return $this->getInstances($data);
    }
    /**
     * Summary of toSql
     * Ritorna la query construita con le clausole.
     * @return string
     */
}
