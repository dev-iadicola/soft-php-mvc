<?php

namespace App\Core\Eloquent;

use App\Core\Eloquent\Schema\Validation\CheckSchema;
use PDO;
use App\Core\Database;
use App\Core\CLI\System\Out;
use App\Core\Exception\ModelNotFoundException;
use App\Core\Exception\ModelStructureException;
use App\Core\Exception\QueryBuilderException;

class QueryBuilder
{
    private static ?QueryBuilder $_instance = null;
    protected array $attribute = [];
    protected ?string $table = null;

    private string $modelName = ''; // Nome del modello, utile per il debug e la gestione degli errori
    private array $fillable = []; // Attributi che possono essere assegnati in massa

    private array $systemColumns = ['id', 'created_at','updated_at'];

    protected string $selectValues = '*'; // Campi da selezionare

    protected string $whereClause = ''; // Clausola WHERE
    protected array $bindings = []; // Parametri di binding
    protected string $orderByClause = ''; // Clausola ORDER BY
    protected string $groupByClause = ''; // Clausola GROUP BY

    private PDO $pdo; // Oggetto PDO per la connessione al database

    public $id = ''; // ID dell'istanza





    /**
     * Summary of attributeExist
     * @param string $name
     * @return bool
     * Permette di verificare se un attributo esiste nell'array $attribute, molto utile per evitare errori 
     * quando si accede a proprietà dinamiche.
     * Questa funzione è utilizzata nei metodi __get e __set per garantire che gli attributi siano validi prima di accedervi 
     * o modificarli.
     */
    private function attributeExist(string $name): bool
    {
        return in_array($name, $this->fillable);
    }

    public function __get($name)
    {
        // Verifica se l'attributo esiste nel Model prima di accedervi
        if (!$this->attributeExist($name)) {
            throw new ModelStructureException("Attribute '$name' does not exist in " . $this->modelName);
        }
        return $this->attribute[$name];
    }

    public function __set($name, $value)
    {
        // Verifica se l'attributo esiste nel Model prima di accedervi
        if (!$this->attributeExist($name)) {
            throw new ModelStructureException("Attribute '$name' does not exist in " . $this->modelName);
        }
        $this->attribute[$name] = $value;
    }

    public function __construct() {}

    public function setPDO(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function setClassModel(string $name)
    {
        $this->modelName = $name;
    }
    public function setTable(string $table)
    {
        if (CheckSchema::tableExist($table))
            $this->table = $table;
        else {
            throw new ModelNotFoundException("Table " + $table + " Not Exist in Schema. Correct yout Model: " + $this->modelName);
        }
    }
    public function setFillable(array $fillable): void
    {
        $this->fillable = $fillable;
    }



    public function where(string $columnName, $parameter): self
    {
        $this->whereClause = "WHERE $columnName = :parameter";
        $this->bindings[':parameter'] = $parameter;
        return $this;
    }

    public function whereNot(string $columnName, $parameter): self
    {


        $this->whereClause = "WHERE NOT $columnName = :parameter";
        $this->bindings[':parameter'] = $parameter;

        return $this;
    }

    public function select(array|string $value): self
    {
        if (is_array($value)) {
            $this->selectValues = implode(', ', $value);
        } else {
            $this->selectValues = $value;
        }
        return $this;
    }

    public function orderBy(string $value): self
    {
        $this->orderByClause = "ORDER BY $value";

        return $this;
    }

    public function groupBy(string $param): self
    {
        $this->groupByClause = "GROUP BY $param";

        return $this;
    }

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

    public function get(int $fetchType = PDO::FETCH_ASSOC): array
    {
        if (empty($this->table)) {
            throw new ModelNotFoundException("Name of table not set. Model: " . $this->modelName);
        }

        $query = "SELECT $this->selectValues FROM $this->table $this->whereClause $this->groupByClause $this->orderByClause";
        $stmt = $this->pdo->prepare($query);

        foreach ($this->bindings as $param => $value) {
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

    private function getOneInstance($data)
    {
        if ($data) {
            $instance = new static();
            $instance->setPDO($this->pdo);
            $instance->setFillable($this->fillable);
            $instance->setTable($this->table);
            $instance->setClassModel($this->modelName);

            foreach ($data as $key => $value) {
                $instance->$key = $value;
            }
            return $instance;
        }

        return null;
    }

    private function getInstances($data)
    {
        $results = [];
        foreach ($data as $row) {
            $instance = new static();
            $instance->setPDO($this->pdo);
            $instance->setFillable($this->fillable);
            $instance->setTable($this->table);
            $instance->setClassModel($this->modelName);
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

    public function getKeyId()
    {
        return $this->id ?: 'id'; // Restituisce il nome della chiave primaria
    }

    public function setKeyId($id)
    {
        $this->id = $id;
    }

    public function getNameTable()
    {
        return $this->table;
    }

    public function find($id, int $fetchType = PDO::FETCH_ASSOC)
    {
        if (empty($this->table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }

        $this->setKeyId($id);
        $id = self::removeSpecialChars($id);

        $query = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch($fetchType);

        return $this->getOneInstance($data);
    }

    public function findOrFail($id, int $fetchType = PDO::FETCH_ASSOC)
    {
        if (empty($this->table)) {
            throw new ModelStructureException("Table name hasn't been set in Mosdel " . $this->modelName);
        }


        $this->setKeyId($id);
        $id = self::removeSpecialChars($id);

        $query = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch($fetchType);

        return $this->getOneInstance($data) ?? throw new ModelNotFoundException($id . " Not Found in Model " . $this->modelName);
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
            throw new ModelStructureException("Table name hasn't been set in Model " . $this->modelName);
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
            throw new ModelStructureException("Table name hasn't been set in Model " . $this->modelName);
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
            throw new ModelStructureException("Table name hasn't been set in Model " . $this->modelName);
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
     * ✅ Valida che le colonne passate a metodi come orderBy() o groupBy() siano sicure.
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
        // Determina automaticamente il contesto (chi ha chiamato questo metodo)
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $trace[1]['function'] ?? 'unknown';

        $allowed = array_merge($this->fillable, $this->systemColumns);
        $validated = [];

        // Normalizza: accetta string o array
        if (is_string($columns)) {
            $columns = [$columns];
        }

        foreach ($columns as $col) {
            $col = trim($col);

            // Blocca SQL injection tramite colonne arbitrarie
            if (!in_array($col, $allowed, true)) {
                throw new QueryBuilderException(
                    "Invalid column '{$col}' passed to {$caller}() in model {$this->modelName}"
                );
            }

            $validated[] = $col;
        }

        // Evita che vengano passate più colonne se non consentito
        if (!$allowMultiple && count($validated) > 1) {
            throw new QueryBuilderException(
                "Multiple columns are not allowed in {$caller}()"
            );
        }

        return $validated;
    }

    //───────────────────────────────────────────────────────────────
    // TRANSAZIONI
    //───────────────────────────────────────────────────────────────

    public function beginTransaction(): void
    {
        if ($this->transactionLevel === 0) {
            $this->pdo->beginTransaction();
        }
        $this->transactionLevel++;
    }

    public function commit(): void
    {
        if ($this->transactionLevel > 0) {
            $this->pdo->commit();
            $this->transactionLevel = 0;
        }
    }

    public function rollBack(): void
    {
        if ($this->transactionLevel > 0) {
            $this->pdo->rollBack();
            $this->transactionLevel = 0;
        }
    }
}
