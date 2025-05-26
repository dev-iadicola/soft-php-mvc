<?php

namespace App\Core\Eloquent;

use App\Core\Eloquent\Schema\Validation\CheckSchema;
use PDO;
use App\Core\Database;
use App\Core\CLI\System\Out;
use App\Core\Exception\ModelNotFoundException;
use App\Core\Exception\ModelStructureException;

class QueryBuilder
{
    protected array $attribute = [];
    protected ?string $table = null;

    protected string $selectValues = '*'; // Campi da selezionare

    protected string $whereClause = ''; // Clausola WHERE
    protected array $bindings = []; // Parametri di binding
    protected string $orderByClause = ''; // Clausola ORDER BY
    protected string $groupByClause = ''; // Clausola GROUP BY

    protected PDO $pdo; // Oggetto PDO per la connessione al database

    public $id = ''; // ID dell'istanza

    public function __construct(PDO $pdo)
    {
        $this->setPDO($pdo);
    }

    
    public function __get($name){
        return $this->attribute[$name];
    }

    public function __set($name, $value){
        $this->attribute[$name] = $value;
    }


    public function setPDO(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function setClassModel(string $name){
        $this->modelName = $name;
    }

    public function setFillable(array $fillable): void
    {
        $this->fillable = $fillable;
    }

    public function setTable(string $table)
    {
        $this->table = $table;
    }

    public function where(string $columnName, $parameter): self
    {
        $columnName = self::removeSpecialChars($columnName);
        $parameter = self::removeSpecialChars($parameter);

        $this->whereClause = "WHERE $columnName = :parameter";
        $this->bindings[':parameter'] = $parameter;

        return $this;
    }

    public function whereNot(string $columnName, $parameter): self
    {
        $columnName = self::removeSpecialChars($columnName);
        $parameter = self::removeSpecialChars($parameter);

        $this->whereClause = "WHERE NOT $columnName = :parameter";
        $this->bindings[':parameter'] = $parameter;

        return $this;
    }

    public function select($value): self
    {
        $sanitizedValue = self::removeSpecialChars($value);
        $this->selectValues = $sanitizedValue;

        return $this;
    }

    public function orderBy(string $value): self
    {
        $sanitizedValue = self::removeSpecialChars($value);
        $this->orderByClause = "ORDER BY $sanitizedValue";

        return $this;
    }

    public function groupBy(string $param): self
    {
        $sanitizeVal = self::removeSpecialChars($param);
        $this->groupByClause = "GROUP BY $sanitizeVal";

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
            throw new \Exception("Nome della tabella non impostato. Model: " .$this->modelName);
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
            $instance = new static($this->pdo);
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
            $instance = new static($this->pdo);
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
        if(CheckSchema::tableExist($this->table)) {
            throw new ModelNotFoundException("Table" . $this->table . " don't exist in database, correct your Model class ". $this->modelName ."");
        }

        $this->setKeyId($id);
        $id = self::removeSpecialChars($id);

        $query = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch($fetchType);

        return $this->getOneInstance($data) ?? throw new ModelNotFoundException( $id . " Not Found in Model " . $this->modelName  );
    }

    public function delete(): bool
    {
        if (empty($this->table)) {
            throw new \Exception("Nome della tabella non impostato.");
        }

        $findRecord = $this->whereClause ?: "WHERE id = :id";
        $query = "DELETE FROM $this->table $findRecord";

        $stmt = $this->pdo->prepare($query);

        foreach ($this->bindings as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        if (!isset($this->bindings[':id']) && !$this->whereClause) {
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
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
        $findRecord = $this->whereClause ?: "WHERE id = :id";
        if (!isset($this->bindings[':id']) && !$this->whereClause) {
            $this->bindings[':id'] = $this->id;
        }

        $query = "UPDATE $this->table SET $setClause $findRecord";
        $stmt = $this->pdo->prepare($query);

        foreach ($values as $field => $value) {
            $stmt->bindValue(":$field", self::removeSpecialChars($value));
        }

        foreach ($this->bindings as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        return $stmt->execute();
    }

    public function create(array $values){
        if (empty($this->table)) {
            throw new ModelStructureException("Table name hasn't been set in Model " . $this->modelName);
        }
    
        $fillable = $this->fillable;
        
       
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


}
