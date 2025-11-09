<?php

namespace App\Core\Eloquent;

use App\Core\Eloquent\Query\Execute;
use App\Core\Eloquent\Query\SqlClauses;
use App\Core\Eloquent\Query\SqlOperation;
use App\Core\Eloquent\Query\Transaction;
use App\Core\Exception\QueryBuilderException;
use App\Traits\Attributes;
use PDO;
use App\Core\Exception\ModelNotFoundException;
use App\Core\Exception\ModelStructureException;
use App\Core\Eloquent\Schema\Validation\CheckSchema;
use PDOStatement;

abstract class AbstractBuilder
{
    use SqlClauses; use SqlOperation;  use Transaction;
    use Execute; use Attributes;
    protected static ?QueryBuilder $_instance = null;
    protected ?string $table = null;
    protected string $modelClass = ''; // Nome del modello, utile per il debug e la gestione degli errori
    protected array $fillable = []; // Attributi che possono essere assegnati in massa
    protected array $bindings = []; // Parametri di binding
    protected array $systemColumns = ['id', 'created_at', 'updated_at'];    
    protected PDO $pdo; // Oggetto PDO per la connessione al database
    public int|float|string|null $id = null; // ID dell'istanza
    private int $paramCounter = 0;

    //───────────────────────────────────────────────────────────────
    //* SETTAGGIO DI BASE 
    //───────────────────────────────────────────────────────────────
    #region SETTERS AND GETTERS
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

    protected function assertTableIsSet(): void
    {
        if (empty($this->table)) {
            throw new ModelStructureException("Name table not set in model $this->modelClass");
        }
    }


    public function setPDO(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function setClassModel(string $name)
    {
        $this->modelClass = $name;
    }
    public function setTable(string $table)
    {
        if (CheckSchema::tableExist($table))
            $this->table = $table;
        else {
            throw new ModelNotFoundException("Table $table Not Exist in Schema. Correct yout Model :  {$this->modelClass} or Schema");
        }
    }
 
    /**
     * Summary of setFillable
     * Utilizzata solo quando viene instanziato il queryBuilder nella classe Model, precisamente nel metodo boot(). 
     * @param array $fillable
     * @return void
     */
    public function setFillable(array $fillable): void
    {
        $this->fillable = $fillable;
    }
    public function getFillable(): array
    {
        return $this->fillable;
    }


    // * Get e Setter di Id
    public function getKeyId(): float|int|string|null
    {
        return $this->id ?: 'id'; // Restituisce il nome della chiave primaria
    }

    public function setKeyId($id)
    {
        $this->id = $id;
    }

    public function getNameTable(): string|null
    {
        return $this->table;
    }


}
