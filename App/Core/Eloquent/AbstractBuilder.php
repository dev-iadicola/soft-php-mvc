<?php

namespace App\Core\Eloquent;

use PDO;
use App\Core\Exception\ModelNotFoundException;
use App\Core\Exception\ModelStructureException;
use App\Core\Eloquent\Schema\Validation\CheckSchema;

abstract class AbstractBuilder
{

    protected static ?QueryBuilder $_instance = null;
    protected array $attribute = [];
    protected ?string $table = null;
    protected string $modelName = ''; // Nome del modello, utile per il debug e la gestione degli errori
    protected array $fillable = []; // Attributi che possono essere assegnati in massa
    protected array $systemColumns = ['id', 'created_at', 'updated_at'];
    protected string $selectValues = '*'; // Campi da selezionare
    protected string $whereClause = ''; // Clausola WHERE
    protected array $bindings = []; // Parametri di binding
    protected string $orderByClause = ''; // Clausola ORDER BY
    protected string $groupByClause = ''; // Clausola GROUP BY
    protected PDO $pdo; // Oggetto PDO per la connessione al database
    public int|float|string|null $id = null; // ID dell'istanza

    private int $paramCounter = 0;

    //───────────────────────────────────────────────────────────────
    // SETTAGGIO DI BASE 
    //───────────────────────────────────────────────────────────────

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

    /**
     * Summary of addBinding
     * @param mixed $val
     * @return mixed
     * Ogni volta che la 
     */


    // * Get e Setter di Id
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

    protected function addValueAndBinding(string $val):mixed{
        $key = ":p_". ++$this->paramCounter;
        $this->bindings[":p_". ++$this->paramCounter ] = $val;
        return $key;
    }

    protected function getPrefix(): string
    {
        return empty($this->whereClause) ? "WHERE" : "AND";
    }

}
