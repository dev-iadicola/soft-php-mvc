<?php

namespace App\Core\Eloquent;

use PDO;
use App\Core\Exception\QueryBuilderException;
use App\Core\Exception\ModelNotFoundException;
use App\Core\Exception\ModelStructureException;

/**
 * Class QueryBuilder
 * _________________________________________
 *
 * Responsible for building and executing dynamic SQL queries
 * using a fluent, secure, and parameterized approach.
 *
 * This class extends {@see AbstractBuilder} and provides
 * high-level methods for defining clauses such as:
 * - SELECT, WHERE, GROUP BY, ORDER BY, LIMIT, etc.
 *
 * It is designed to be used internally by Models but can
 * also be utilized directly for custom query operations.
 *
 * Note:
 * The Singleton pattern was initially considered but removed
 * to avoid shared-state issues between different queries.
 *
 * @package App\Core\Eloquent
 */

class QueryBuilder extends AbstractBuilder
{


    public function exists(): bool
    {
        $sql = "SELECT EXISTS(" . $this->toSql() . ")";
        return (bool) $this->fetchColumn();
    }

    //* ───────────────────────────────────────────────────────────────
    #region GETTER
    //* ───────────────────────────────────────────────────────────────
    /**
     * Summary of get
     * @param int $fetchType
     * @throws \App\Core\Exception\ModelNotFoundException
     * @return array<Model>
     */
    public function get(int $fetchType = PDO::FETCH_ASSOC): array
    {
        if (empty($this->table)) {
            throw new ModelNotFoundException("Name of table not set. Model: " . $this->modelClass);
        }
        $rows =  $this->fetchAll($fetchType);
        return $this->getInstances($rows);
    }

    /**
     * 
     * 
     * @param int $fetchType
     * @throws \Exception
     * @return Model|null
     */
    public function first(int $fetchType = PDO::FETCH_ASSOC)
    {
        if (empty($this->table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }

        $this->limit(1);

        $rows =  $this->fetch(fetchTyep: $fetchType);

        return $this->getOneInstance($rows);
    }

    public function find(int|float|string $id, int $fetchType = PDO::FETCH_ASSOC)
    {
        // Verifica che nel Model il nome della tabella sia settato
        $this->assertTableIsSet();

        $this->setKeyId($id);
        $id = self::removeSpecialChars($id);

        // Richiamo il metodo where. Che popola a sua volta bindings
        $this->where('id', $id);

        // eseguo le operazioni prepare, binding, execute della query e il fetch ritorna di base un array con risultato della query    
        $rows = $this->fetch($fetchType);
        return $this->getOneInstance($rows);
    }



    private function getOneInstance(array|bool|null $rows): Model|null
    {
        // se la query non ritorna la riga di risultato tramite query, ritorna null
        if (!$rows) {
            return null;
        }
        // instanzio il model al quale viene effettuato il querybuilder
        $model = new $this->modelClass;
        // * Important
        $model->setQueryBuilder($this);
        foreach ($rows as $key => $value) {
            $model->$key =  $value;
        }
        return $model;
    }

    private function getInstances($rows): array
    {
        $arrayModels = [];
        foreach ($rows as $row) {
            $model = $this->getOneInstance($row);
            $arrayModels[] = $model;
        }
        return $arrayModels;
    }

    // * Create new record in DB and get the id of new record.
    public function insertGetId(array $values): int
    {
        $this->create($values);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Summary of findAll
     * @param int $fetchType
     * @throws \Exception
     * @return array<Model>
     */
    public function findAll(int $fetchType = PDO::FETCH_ASSOC): array
    {
        if (empty($this->table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }
        $rows = $this->fetchAll($fetchType);
        return $this->getInstances($rows);
    }


    public function findOrFail($id, int $fetchType = PDO::FETCH_ASSOC)
    {
        if (empty($this->table)) {
            throw new ModelStructureException("Table name hasn't been set in Mosdel " . $this->modelClass);
        }
        $this->setKeyId($id);
        $id = self::removeSpecialChars($id);
        $this->where('id', $id);
        return $this->fetch() ?? throw new ModelNotFoundException($id . " Not Found in Model " . $this->modelClass);
    }



    //* ───────────────────────────────────────────────────────────────
    #region SETTER UPDATE
    //* ───────────────────────────────────────────────────────────────
    public function update(array $values): self
    {
        if (empty($this->table)) {
            throw new ModelStructureException("Table name hasn't been set in Model " . $this->modelClass);
        }

        $fillable = $this->fillable;
        if (!empty($fillable)) {
            $values = array_filter($values, fn($key) => in_array($key, $fillable), ARRAY_FILTER_USE_KEY);
        }

        $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($values)));

        $whereClause = $this->whereClause ?: "WHERE id = :id";
        if (!isset($this->bindings[':id']) && !$this->whereClause) {
            $this->bindings[':id'] = $this->id;
        }
        // TODO: IMPLEMENTARE STESSA cosa del delete str replace e sostituire SELECT con UPDATE 
        $query = "UPDATE {$this->table} SET {$setClause} {$whereClause}";
        $stmt = $this->pdo->prepare($query);


        // Bind dei valori da aggiornare
        foreach ($values as $key => $val) {
            // Fixed for php 8.4. (verifica che sia una stringa prima di fare null)
            if (is_string($val) && trim($val) === '') {
                $val = NULL;
            }
            $stmt->bindValue(":$key", $val);
        }

        // Bind dei valori nella WHERE
        foreach ($this->bindings as $param => $value) {
            if (!isset($values[ltrim($param, ':')])) {
                $stmt->bindValue($param, $value);
            }
        }

        $stmt->execute();
        return $this;
    }

    private function fill(array $values)
    {
        $fillable = $this->fillable;

        // Filtra i valori per tenere solo quelli presenti in $fillable
        return $filteredValues = array_filter(
            $values,
            fn($key) => in_array($key, $fillable),
            ARRAY_FILTER_USE_KEY
        );
    }
    public function create(array $values)
    {
        if (empty($this->table)) {
            throw new ModelStructureException("Table name hasn't been set in Model " . $this->modelClass);
        }

        // Filtra i valori per tenere solo quelli presenti in $fillable
        $filteredValues = $this->fill($values);

        if (empty($filteredValues)) {
            throw new \InvalidArgumentException("Impossible enter any value.");
        }

        // Prepara colonne e placeholder per PDO
        $columns = implode(", ", array_keys($filteredValues));
        $placeholders = implode(", ", array_map(fn($key) => ":$key", array_keys($filteredValues)));

        $query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        $stmt = $this->pdo->prepare($query);

        // Bind dei valori
        foreach ($filteredValues as $key => $val) {
            $stmt->bindValue(":$key", $val);
        }

        $stmt->execute();
        return $this;
    }



    //* ───────────────────────────────────────────────────────────────
    #region DELETE
    //* ───────────────────────────────────────────────────────────────
    public function delete(): bool
    {

        if (empty($this->table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }
        if (empty($this->whereClause)) {
            if (!isset($this->id)) {
                throw new QueryBuilderException('No condition was selected in the delete action. For security reasons, it is not possible to delete all records in a table.');
            }
        }
        // TODO: imoplementare con str replace select clause, sostiuire select con delete. 
        $query = "DELETE FROM {$this->table} $this->whereClause";

        $stmt = $this->pdo->prepare($query);

        foreach ($this->bindings as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        return $stmt->execute();
    }
    // public function save(array $values): bool
    // {
    //     if (empty($this->table)) {
    //         throw new ModelStructureException("Table name hasn't been set in Model " . $this->modelClass);
    //     }
    //     $fillable = $this->fillable;
    //     if (!empty($fillable)) {
    //         $values = array_filter($values, fn($key) => in_array($key, $fillable), ARRAY_FILTER_USE_KEY);
    //     }

    //     $values = array_map(fn($value) => self::removeSpecialChars($value), $values);
    //     $keys = array_keys($values);
    //     $fields = implode(',', $keys);
    //     $placeholders = implode(',', array_map(fn($key) => ":$key", $keys));

    //     $query = "INSERT INTO $this->table ($fields) VALUES ($placeholders)";
    //     $stmt = $this->pdo->prepare($query);

    //     foreach ($values as $field => $fieldValue) {
    //         $stmt->bindValue(":$field", $fieldValue);
    //     }

    //     return $stmt->execute();
    // }

    //* ───────────────────────────────────────────────────────────────
    #region UTILS
    //* ───────────────────────────────────────────────────────────────
    public static function removeSpecialChars(string $value): string
    {
        return htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');
    }


    /**
     * Valida che le colonne passate a metodi come orderBy() o groupBy() siano sicure.
     * 
     * Determina automaticamente il nome del metodo chiamante tramite debug_backtrace()
     * per restituire messaggi d’errore più precisi.
     *
     * @param array|string $columns  Colonne da validare
     * @param bool $allowMultiple    Permette più colonne (true per groupBy)
     * @return array                 Array di colonne validate
     * @deprecated 
     * @throws QueryBuilderException Se una colonna non è ammessa
     */
    protected function validateColumns(array|string $columns, bool $allowMultiple = false): array
    {
        // Ottieni il backtrace completo per risalire al chiamante reale
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $caller = null;

        // Trova il primo frame che non appartiene al core del framework
        foreach ($trace as $frame) {
            if (isset($frame['file']) && !str_contains($frame['file'], 'App/Core/Eloquent')) {
                $caller = $frame;
                break;
            }
        }

        // Determina il nome della funzione chiamante e l’origine del file
        $callerName = $trace[1]['function'] ?? 'unknown';
        $originInfo = $caller
            ? " (called from {$caller['file']} on line {$caller['line']})"
            : '';

        // Colonne consentite (fillable + systemColumns)
        $allowed = array_merge($this->fillable, $this->systemColumns);
        $validated = [];

        // Normalizza: accetta stringa singola o array
        if (is_string($columns)) {
            $columns = [$columns];
        }

        foreach ($columns as $col) {
            $col = trim($col);

            // Blocca SQL injection tramite colonne arbitrarie
            if (!in_array($col, $allowed, true)) {
                throw new QueryBuilderException(
                    "Invalid column '{$col}' passed to {$callerName}() in model {$this->modelClass}{$originInfo}"
                );
            }

            $validated[] = $col;
        }

        // Evita che vengano passate più colonne se non consentito
        if (!$allowMultiple && count($validated) > 1) {
            throw new QueryBuilderException(
                "Multiple columns are not allowed in {$callerName}() in model {$this->modelClass}{$originInfo}"
            );
        }

        return $validated;
    }
    public function toArray(): array
    {
        return array_map(fn($m) => get_object_vars($m), $this->get());
    }


    //───────────────────────────────────────────────────────────────
    #region CLONE    
    //───────────────────────────────────────────────────────────────
    public function duplicate(): static
    {
        return clone $this;
    }
}
