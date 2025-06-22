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

        // condizioni
        $where = $this->whereClause ?? '';
        $bindigns = $this->bindings == [];

        if(empty($where)){
            if(isset($this->id)){
                $where = "WHERE id = :id";
                $bindigns = [':id' => $this->id];
            }else{
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
            if(trim($val) === ''){
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


}
