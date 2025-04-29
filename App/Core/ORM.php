<?php

namespace App\Core;

use PDO;

/**
 * Classe base per la gestione dei Model.
 */
class ORM
{
    protected static ?PDO $pdo = null; // Connessione PDO
    protected static string $table = ''; // Nome della tabella
    protected static array $fillable = []; // Campi riempibili
    protected  $selectValues = '*';

    protected string $whereClause = ''; // Clausola WHERE
    protected array $bindings = []; // Parametri di binding
    protected string $orderByClause = ''; // Clausola ORDER BY


    public string $groupByClause = '';

    public  $id = '';

    public function __construct(PDO $pdo)
    {
        self::setPDO($pdo);
    }

    public static function setPDO(PDO $pdo)
    {
        self::$pdo = $pdo;
    }

    public static function setTable(string $table)
    {
        self::$table = $table;
    }

    public static function where(string $columnName, $parameter): self
    {
        $columnName = self::removeSpecialChars($columnName);
        $parameter = self::removeSpecialChars($parameter);

        $instance = new static(self::$pdo);
        $instance->whereClause = "WHERE $columnName = :parameter";
        $instance->bindings[':parameter'] = $parameter;

        return $instance;
    }

    public static function whereNot(string $columnName, $parameter): self
    {
        $columnName = self::removeSpecialChars($columnName);
        $parameter = self::removeSpecialChars($parameter);

        $instance = new static(self::$pdo);
        $instance->whereClause = "WHERE NOT $columnName = :parameter";
        $instance->bindings[':parameter'] = $parameter;

        return $instance;
    }
    public function select($value): self
    {
        $sanitizedValue = self::removeSpecialChars($value);
        $instance = new static(self::$pdo);
        $instance->selectValues = $sanitizedValue;
        return $instance;
    }


    public static function orderBy(string $value): self
    {
        $sanitizedValue = self::removeSpecialChars($value);

        $instance = new static(self::$pdo);
        $instance->orderByClause = "ORDER BY $sanitizedValue";

        return $instance;
    }

    public  function groupBy(string $param)
    {
        $sanitizeVal = self::removeSpecialChars($param);

        $this->groupByClause = " GROUP BY $sanitizeVal ";

        return $this;
    }

    public static function query(string $query, array $params = [], int $fetchType = PDO::FETCH_ASSOC): array
    {
        $stmt = self::$pdo->prepare($query);

        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        $stmt->execute();

        $data = $stmt->fetchAll($fetchType);
        return self::getIstance($data);
    }
    public function get(int $fetchType = PDO::FETCH_ASSOC): array
    {
        $table = static::$table;
        if (empty($table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }



        $query = "SELECT " .  $this->selectValues . " FROM " . $table . " " . $this->whereClause . " " .  $this->groupByClause . " " . $this->orderByClause;
        $st = self::$pdo->prepare($query);

        foreach ($this->bindings as $param => $value) {
            $st->bindValue($param, $value);
        }

        $st->execute();
        $data = $st->fetchAll($fetchType);


        return self::getIstance(data: $data);
    }






    public function first(int $fetchType = PDO::FETCH_ASSOC)
    {
        $table = static::$table;
        if (empty($table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }

        $query = "SELECT * FROM " . $table . " " . $this->whereClause . " " . $this->orderByClause  . " LIMIT 1";
        $st = self::$pdo->prepare($query);

        foreach ($this->bindings as $param => $value) {
            $st->bindValue($param, $value);
        }

        $st->execute();
        $data = $st->fetch($fetchType);

        return self::getOneIstance(data: $data);
    }

    private static function getOneIstance($data)
    {
        if ($data) {
            // Restituisci un'istanza della classe corrente
            $instance = new static(self::$pdo);
            foreach ($data as $key => $value) {
                $instance->$key = $value;
            }
            return $instance;
        }

        return null;
    }
    private static function getIstance($data)
    {
        $results = [];
        foreach ($data as $row) {
            $instance = new static(self::$pdo);
            foreach ($row as $key => $value) {
                $instance->$key = $value;
            }
            $results[] = $instance;
        }
        return $results;
    }

    public static function findAll(int $fetchType = PDO::FETCH_ASSOC): array
    {
        $table = static::$table;
        if (empty($table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }

        $query = "SELECT * FROM " . $table;
        $st = self::$pdo->prepare($query);
        $st->execute();
        $data = $st->fetchAll($fetchType);



        return self::getIstance(data: $data);
    }


    public  function setId($id)
    {
        $this->$id = $id;
    }



    public static function find($id, int $fetchType = PDO::FETCH_ASSOC)
    {
        $table = static::$table;
        $instance = new static(self::$pdo);
        $instance->setId($id);

        $id = self::removeSpecialChars($id);

        $query = "SELECT * FROM " . $table . " WHERE id = :id";
        $st = self::$pdo->prepare($query);
        $st->bindParam(':id', $id, PDO::PARAM_INT);
        $st->execute();
        $data = $st->fetch($fetchType);
        return self::getOneIstance($data);
    }

    public function delete(): bool
    {
        $table = static::$table;

        if (empty($table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }

        // Costruisci la query di eliminazione
        $findRecord = $this->whereClause ? $this->whereClause : " WHERE id = :id";
        $query = "DELETE FROM $table $findRecord";

        // Prepara la query con PDO
        $stmt = self::$pdo->prepare($query);

        // Associa i parametri della clausola WHERE
        foreach ($this->bindings as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        // Se non c'è una clausola WHERE e ':id' non è già impostato, imposta ':id'
        if (!isset($this->bindings[':id']) && !$this->whereClause) {
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        }

        // Esegui la query
        return $stmt->execute();
    }


    public function update(array $values): bool
    {
        $table = static::$table;
        if (empty($table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }

        $fillable = static::$fillable;
        if (!empty($fillable)) {
            $values = array_filter($values, fn ($key) => in_array($key, $fillable), ARRAY_FILTER_USE_KEY);
        }

        // Imposta la clausola SET
        $setClause = implode(', ', array_map(fn ($key) => "$key = :$key", array_keys($values)));

        // Controlla e imposta la clausola WHERE
        $findRecord = $this->whereClause ? $this->whereClause : " WHERE id = :id";
        if (!isset($this->bindings[':id']) && !$this->whereClause) {
            $this->bindings[':id'] = $this->id;
        }

        // Prepara la query
        $query = "UPDATE $table SET $setClause $findRecord";
        $stmt = self::$pdo->prepare($query);

        // Associa i valori dei parametri nella clausola SET
        foreach ($values as $field => $value) {
            $stmt->bindValue(":$field", self::removeSpecialChars($value));
        }

        // Associa i valori dei parametri nella clausola WHERE
        foreach ($this->bindings as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        // Esegui la query
        return $stmt->execute();
    }

    public function dirtyUpdate(array $values): bool
    {
        $table = static::$table;
        if (empty($table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }

        $fillable = static::$fillable;
        if (!empty($fillable)) {
            $values = array_filter($values, fn ($key) => in_array($key, $fillable), ARRAY_FILTER_USE_KEY);
        }

        // Rimuovi solo i tag PHP dai valori
        $values = array_map([self::class, 'removePhpTags'], $values);



        // Imposta la clausola SET
        $setClause = implode(', ', array_map(fn ($key) => "$key = :$key", array_keys($values)));

        // Controlla e imposta la clausola WHERE
        $findRecord = $this->whereClause ? $this->whereClause : " WHERE id = :id";
        if (!isset($this->bindings[':id']) && !$this->whereClause) {
            $this->bindings[':id'] = $this->id;
        }

        // Prepara la query
        $query = "UPDATE $table SET $setClause $findRecord";
        $stmt = self::$pdo->prepare($query);

        // Associa i valori dei parametri nella clausola SET
        foreach ($values as $field => $value) {
            $stmt->bindValue(":$field", $value, PDO::PARAM_STR);
        }

        // Associa i valori dei parametri nella clausola WHERE
        foreach ($this->bindings as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        // Esegui la query
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error executing query: " . $e->getMessage());
            throw $e;
        }
    }



    /**
     * Summary of save
     * @param array $values
     * @throws \Exception
     * @return bool
     * 
     * Crea un nuovo elemento del database. 
     */
    public static function save(array $values): bool
    {
        $table = static::$table;
        if (empty($table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }

        $fillable = static::$fillable;

        if (!empty($fillable)) {
            $values = array_filter($values, fn ($key) => in_array($key, $fillable), ARRAY_FILTER_USE_KEY);
        }

        $values = array_map(fn ($value) => self::removeSpecialChars($value), $values);
        $keys = array_keys($values);
        $fields = implode(',', $keys);
        $placeholders = implode(',', array_map(fn ($key) => ":$key", $keys));

        $query = "INSERT INTO $table ($fields) VALUES ($placeholders)";
        $stmt = self::$pdo->prepare($query);

        foreach ($values as $field => $fieldValue) {
            $stmt->bindValue(":$field", $fieldValue);
        }

        return $stmt->execute();
    }

    public static function dirtySave(array $values): bool
    {
        // Assicurati che la tabella sia definita
        $table = static::$table;
        if (empty($table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }

        // Filtra i valori per includere solo quelli che sono nel $fillable
        $fillable = static::$fillable;
        if (!empty($fillable)) {
            $values = array_filter($values, fn ($key) => in_array($key, $fillable), ARRAY_FILTER_USE_KEY);
        }

        // Rimuovi i tag PHP dai valori
        $values = array_map([self::class, 'removePhpTags'], $values);

        // Crea la query di inserimento
        $keys = array_keys($values);
        $fields = implode(',', $keys);
        $placeholders = implode(',', array_map(fn ($key) => ":$key", $keys));
        $query = "INSERT INTO $table ($fields) VALUES ($placeholders)";

        // Prepara e esegui la query
        $stmt = self::$pdo->prepare($query);

        foreach ($values as $field => $fieldValue) {
            $stmt->bindValue(":$field", $fieldValue, PDO::PARAM_STR);
        }

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            // Logga l'errore e rilancia l'eccezione
            error_log("Error executing query: " . $e->getMessage() . " at line" . $e->getLine() . ".". $e->getCode());
            throw $e;
        }
    }

    private static function removePhpTags(string $value): string
    {
        // Rimuove i tag PHP dai valori
        return preg_replace('/<\?php.*?\?>/s', '', $value);
    }


    public static function removeSpecialChars($input): string
    {
        $cleaned = strip_tags($input);
        $cleaned = htmlspecialchars($cleaned, ENT_QUOTES, 'UTF-8');

        return $cleaned;
    }
}
