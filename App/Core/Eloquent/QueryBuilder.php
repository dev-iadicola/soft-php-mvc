<?php

namespace App\Core\Eloquent;

use PDO;
use ErrorException;
use App\Core\Database;
use App\Core\Helpers\Log;
use App\Core\CLI\System\Out;
use App\Core\Exception\QueryBuilderException;
use App\Core\Exception\ModelNotFoundException;
use App\Core\Exception\ModelStructureException;
use App\Core\Eloquent\Schema\Validation\CheckSchema;

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
   
    #region COSTRUZIONE QUERY

    /**
     * Summary of where
     * 
     * Aggiunge una clausola Where alla query
     * 
     *  Semplice confronto di uguaglianza:
     *    ```php
     *    $query->where('id', 5);
     *     genera: WHERE id = :param
     *    ```
     * 
     *  Con operatore personalizzato:
     *    ```php
     *    $query->where('prezzo', '>', 100);
     *     genera: WHERE prezzo > :param
     *    ```
     * @param string $columnName Nome della colonna su cui applicare la condizione
     * @param string|int|float|bool|null $conditionOrValue Operatore di contonto SQL oppure valore se non è specificato l'operatore
     * @param  string|int|float|bool|null $value Valore del parametro, opzionale se il secondo parametro non è inserito.
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

    /**
     * Summary of whereNot
     * 
     * Aggiunge una clausola Where NOT alla query
     * 
     *  Semplice confronto di uguaglianza:
     *    ```php
     *    $query->where('id', 5);
     *     genera: WHERE id = :param
     *    ```
     * 
     *  Con operatore personalizzato:
     *    ```php
     *    $query->where('prezzo', '>', 100);
     *     genera: WHERE prezzo > :param
     *    ```
     * @param string $columnName Nome della colonna su cui applicare la condizione
     * @param string|int|float|bool|null $value  Valore del parametro
     * @param  string|int|float|bool|null $value Valore del parametro, opzionale se il secondo parametro non è inserito.
     * @return QueryBuilder 
     */
    public function whereNot(string $columnName, string $value, $parameter = null): self
    {
        if ($parameter === null) {
            $this->whereClause .= "{$this->getPrefix()} $columnName <> {$this->AddBind($value)} ";
        }
        return $this;
    }
    /**
     * Summary of getPrefix
     * @return string Se la clausola where è vuota, ritorna WHERE altrimenti concatena le altre condizioni con AND
     */
   
    /**
     * Imposta i campi da selezionare nella query SQL.
     * 
     * Può ricevere una stringa singola o un array di colonne:
     * 
     *  Stringa singola:
     *    ```php
     *    $query->select('id, nome, email');
     *      genera: SELECT id, nome, email
     *    ```
     * 
     * Array di colonne:
     *    ```php
     *    $query->select(['id', 'nome', 'email']);
     *     genera: SELECT id, nome, email
     *    ```
     * 
     * Se non viene specificato alcun campo, la query di default selezionerà `*`.
     * 
     * @param array<string>|string $value Elenco dei campi da selezionare.
     * 
     * @return self Ritorna l’istanza corrente per permettere chiamate fluide.
     */
    public function select(array|string $value): self
    {
        if (is_array($value)) {
            $this->selectValues = implode(', ', $value);
        } else {
            $this->selectValues = $value;
        }
        return $this;
    }

    /**
     * Summary of orderBy
     * @param array<string>|string $columns
     * @param string $direction
     * @throws \App\Core\Exception\QueryBuilderException
     * @return QueryBuilder
     */
    public function orderBy(array|string $columns, string $direction = 'ASC'): self
    {
        if (!empty($this->orderByClause)) throw new QueryBuilderException("You can't use OrderBy() more than once in the same query for model {$this->modelClass} ");
        // validazione delle colonne
        $validated = $this->validateColumns($columns, true);

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


    /**
     * Raggruppa i risultati per una o più colonne.
     *
     * Utilizza validateColumns() per garantire che le colonne siano ammesse
     * (presenti in $fillable o $systemColumns).
     * 
     * Se groupBy() viene richiamato più di una volta nella stessa query,
     * viene lanciata un'eccezione per evitare ambiguità.
     *
     * @param string|array<string> $columns  Una o più colonne per la clausola GROUP BY
     * @return self
     *
     * @throws QueryBuilderException Se le colonne non sono valide o se il metodo viene richiamato più volte
     */
    public function groupBy(string|array $columns): self
    {
        // Evita l’uso multiplo
        if (!empty($this->groupByClause)) {
            throw new QueryBuilderException(
                "You can't use groupBy() more than once in the same query for model {$this->modelClass}"
            );
        }

        // Validazione colonne con context automatico
        $validated = $this->validateColumns($columns, true);

        // Costruisce la clausola SQL
        $this->groupByClause = 'GROUP BY ' . implode(', ', $validated);

        return $this;
    }


    /**
     * Summary of query 
     * 
     * Prepara la tua query. Questo metodo è molto utile se si tratta di una query complessa. 
     * 
     * @param string $query 
     * @param array<string> $params parametri da serire per il bindValue
     * @param int $fetchType
     * @return QueryBuilder[]
     */
    public function query(string $query, array $params = [], int $fetchType = PDO::FETCH_ASSOC): array
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
     * 
     * Ritorna la query in stringa
     * @return string
     */
    
    public function get(int $fetchType = PDO::FETCH_ASSOC): array
    {
        if (empty($this->table)) {
            throw new ModelNotFoundException("Name of table not set. Model: " . $this->modelClass);
        }

        $query = $this->toSql();
        $stmt = $this->pdo->prepare($query);

        foreach ($this->bindings as $param => $value) {
           Log::debug("key $param => value $value");
            $stmt->bindValue($param, $value);
        }

        $stmt->execute();
        $data = $stmt->fetchAll($fetchType);

        return $this->getInstances($data);
    }

    public function first(int $fetchType = PDO::FETCH_ASSOC)
    {
        if (empty($this->table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }

        $query = "SELECT * FROM $this->table $this->whereClause $this->orderByClause LIMIT 1";
        $stmt = $this->pdo->prepare($query);

        foreach ($this->bindings as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        $stmt->execute();
        $data = $stmt->fetch($fetchType);

        return $this->getOneInstance($data);
    }

    public function find(int|float|string $id)
    {   
        // Verifica che nel Model il nome della tabella sia settato
        $this->assertTableIsSet();

        $this->setKeyId($id);
        $id = self::removeSpecialChars($id);

        // Richiamo il metodo where. Che popola a sua volta bindings
        $this->where('id', $id);

        
        return $this->getOneInstance($this->executeQuery());
    }

    private function prepare() {}

    private function getOneInstance($data): QueryBuilder|null
    {
        if ($data) {
            $instance = new static();
            $instance->setPDO($this->pdo);
            $instance->setFillable($this->fillable);
            $instance->setTable($this->table);
            $instance->setClassModel($this->modelClass);

            foreach ($data as $key => $value) {
                $instance->$key = $value;
            }
            return $instance;
        }

        return null;
    }

    private function getInstances($data): array
    {
        $results = [];
        foreach ($data as $row) {
            $instance = new static();
            $instance->setPDO($this->pdo);
            $instance->setFillable($this->fillable);
            $instance->setTable($this->table);
            $instance->setClassModel($this->modelClass);
            foreach ($row as $key => $value) {
                $instance->$key = $value;
            }
            $results[] = $instance;
        }
        return $results;
    }

    public function findAll(int $fetchType = PDO::FETCH_ASSOC): array
    {
        if (empty($this->table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }

        $query = "SELECT * FROM $this->table";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll($fetchType);

        return $this->getInstances($data);
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

        // condizioni
        $where = $this->whereClause ?? '';
        $bindigns = $this->bindings = [];

        if (empty($where)) {
            if (isset($this->id)) {
                $where = "WHERE id = :id";
                $bindigns = [':id' => $this->id];
            } else {
                throw new QueryBuilderException('No condition was selected in the delete action. For security reasons, it is not possible to delete all records in a table.');
            }
        }

        $query = "DELETE FROM {$this->table} $where";

        $stmt = $this->pdo->prepare($query);

        foreach ($bindigns as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        return $stmt->execute();
    }

    public function update(array $values): bool
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

        $query = "UPDATE {$this->table} SET {$setClause} {$whereClause}";
        $stmt = $this->pdo->prepare($query);

        // Bind dei valori da aggiornare
        foreach ($values as $key => $val) {
            if (trim($val) === '') {
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

        return $stmt->execute();
    }


    public function create(array $values)
    {
        if (empty($this->table)) {
            throw new ModelStructureException("Table name hasn't been set in Model " . $this->modelClass);
        }

        $fillable = $this->fillable;

        // Filtra i valori per tenere solo quelli presenti in $fillable
        $filteredValues = array_filter(
            $values,
            fn($key) => in_array($key, $fillable),
            ARRAY_FILTER_USE_KEY
        );

        if (empty($filteredValues)) {
            throw new \InvalidArgumentException("Nessun valore valido da inserire.");
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

    public function save(array $values): bool
    {
        if (empty($this->table)) {
            throw new ModelStructureException("Table name hasn't been set in Model " . $this->modelClass);
        }
        $fillable = $this->fillable;
        if (!empty($fillable)) {
            $values = array_filter($values, fn($key) => in_array($key, $fillable), ARRAY_FILTER_USE_KEY);
        }

        $values = array_map(fn($value) => self::removeSpecialChars($value), $values);
        $keys = array_keys($values);
        $fields = implode(',', $keys);
        $placeholders = implode(',', array_map(fn($key) => ":$key", $keys));

        $query = "INSERT INTO $this->table ($fields) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($query);

        foreach ($values as $field => $fieldValue) {
            $stmt->bindValue(":$field", $fieldValue);
        }

        return $stmt->execute();
    }

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
