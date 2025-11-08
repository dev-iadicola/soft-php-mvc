<?php

namespace App\Core\Eloquent;

use PDO;
use App\Core\Exception\QueryBuilderException;
use App\Core\Exception\ModelNotFoundException;
use App\Core\Exception\ModelStructureException;

/**
 * Classe QueryBuilder
 *
 * Responsabile della costruzione ed esecuzione dinamica delle query SQL
 * utilizzando un approccio fluente e sicuro tramite parametri bindati.
 *
 * Questa classe estende {@see AbstractBuilder} e fornisce metodi
 * di alto livello per la definizione di clausole come:
 * - SELECT, WHERE, GROUP BY, ORDER BY, LIMIT, ecc.
 *
 * È progettata per essere utilizzata internamente dai Model, ma può
 * essere usata anche in modo diretto per query personalizzate.
 *
 * Nota:
 * In una versione iniziale era previsto l'utilizzo del pattern Singleton,
 * ma è stato rimosso per evitare problemi di stato condiviso tra query diverse.
 *
 * @package App\Core\Eloquent
 */
class QueryBuilder extends AbstractBuilder
{
    

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

        $this->limitClause = "LIMIT 1";

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
            // instanzio classe model 
            $model = new $this->modelClass;
            //popolo secondo i nuovi dati rows l'array attrebutes ma utilizzo i setter magici per farlo
            foreach ($row as $key => $value) {
                // andiamo sul sicuro utilizzando __set anziché direttamente la chiave in modo da non chiamare prorpeità del model, popolando bene l'array attributes
                $model->__set($key, $value);
            }

            $arrayModels[] = $model;
            //aggiunfo il model su result
        }
        return $arrayModels;
       

    }

    public function findAll(int $fetchType = PDO::FETCH_ASSOC): array
    {
        if (empty($this->table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }

        // $query = "SELECT * FROM $this->table";
        // $stmt = $this->pdo->prepare($query);
        // $stmt->execute();
        // $rows = $stmt->fetchAll($fetchType);
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

        $query = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch($fetchType);

        return $this->getOneInstance($data) ?? throw new ModelNotFoundException($id . " Not Found in Model " . $this->modelClass);
    }

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

        $fillable = $this->fillable;

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



    //───────────────────────────────────────────────────────────────
    // TRANSAZIONI
    //───────────────────────────────────────────────────────────────
    private int $transactionLevel = 0;

    /**
     * Avvia una transazione, gestendo livelli annidati.
     */
    public function beginTransaction(): void
    {
        try {
            if ($this->transactionLevel === 0) {
                $this->pdo->beginTransaction();
            } else {
                // Usa SAVEPOINT per le transazioni annidate (se supportate)
                $this->pdo->exec("SAVEPOINT LEVEL{$this->transactionLevel}");
            }

            $this->transactionLevel++;
        } catch (\PDOException $e) {
            \App\Core\Helpers\Log::exception($e);
            throw $e;
        }
    }

    /**
     * Conferma la transazione o rilascia un savepoint.
     */
    public function commit(): void
    {
        if ($this->transactionLevel === 0) {
            return; // nessuna transazione attiva
        }

        $this->transactionLevel--;

        try {
            if ($this->transactionLevel === 0) {
                $this->pdo->commit();
            } else {
                // Rilascia il savepoint invece di committare tutto
                $this->pdo->exec("RELEASE SAVEPOINT LEVEL{$this->transactionLevel}");
            }
        } catch (\PDOException $e) {
            \App\Core\Helpers\Log::exception($e);
            throw $e;
        }
    }

    /**
     * Annulla la transazione o ripristina un savepoint.
     */
    public function rollBack(): void
    {
        if ($this->transactionLevel === 0) {
            return;
        }

        $this->transactionLevel--;

        try {
            if ($this->transactionLevel === 0) {
                $this->pdo->rollBack();
            } else {
                // Ripristina lo stato al savepoint precedente
                $this->pdo->exec("ROLLBACK TO SAVEPOINT LEVEL{$this->transactionLevel}");
            }
        } catch (\PDOException $e) {
            \App\Core\Helpers\Log::exception($e);
            throw $e;
        }
    }

    /**
     * Restituisce true se c'è una transazione attiva.
     */
    public function inTransaction(): bool
    {
        return $this->transactionLevel > 0 && $this->pdo->inTransaction();
    }
}
