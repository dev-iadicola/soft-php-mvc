<?php

namespace App\Core\Eloquent;

use App\Core\Exception\QueryBuilderException;
use PDO;
use App\Core\Exception\ModelNotFoundException;
use App\Core\Exception\ModelStructureException;
use App\Core\Eloquent\Schema\Validation\CheckSchema;
use PDOStatement;

abstract class AbstractBuilder
{

    protected static ?QueryBuilder $_instance = null;
    protected ?string $table = null;
    protected string $modelClass = ''; // Nome del modello, utile per il debug e la gestione degli errori
    protected array $fillable = []; // Attributi che possono essere assegnati in massa
    protected array $systemColumns = ['id', 'created_at', 'updated_at'];
    protected string $selectValues = '*'; // Campi da selezionare
    protected string $whereClause = ''; // Clausola WHERE
    protected array $bindings = []; // Parametri di binding
    protected string $orderByClause = ''; // Clausola ORDER BY
    protected string $groupByClause = ''; // Clausola GROUP BY
    protected string $limitClause = ""; // Clausola Limit
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
            throw new ModelNotFoundException("Table " + $table + " Not Exist in Schema. Correct yout Model: " + $this->modelClass);
        }
    }
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


    #ENDREGION

    //───────────────────────────────────────────────────────────────
    //* METODI SMART PER POPOLAZIONE STATEMENT SQL 
    //───────────────────────────────────────────────────────────────
    #region SQL OPERAZIONI
    protected function AddBind(?string $val = null): mixed
    {   
        if($val=== null) throw new QueryBuilderException("The value is NULL");
        $key = ":p_" . ++$this->paramCounter;
        $this->bindings[":p_" . $this->paramCounter] = $val;
        return $key;
    }
    /**
     * Summary of getPrefix
     * @return string
     */
    protected function getPrefix(): string
    {
        return empty($this->whereClause) ? "WHERE" : "AND";
    }

    /**
     * Summary of toSql
     * Ritorna la query construita con le clausole.
     * @return string
     */
    public function toSql(): string
    {
        return "SELECT $this->selectValues FROM $this->table $this->whereClause $this->groupByClause $this->orderByClause $this->limitClause";
    }

    /**
     * Summary of executeQuery
     * 
     * prendo la query con i paramentri da sostituire.
     * 
     * Preparo lo statement della stringa con i parametri.
     *
     * ciclo l'array @property array<string,string> $this->bindings 
     * per allegare i parametri ai bindings
     *  
     * 
     * 
     * @return bool
     * */

    //───────────────────────────────────────────────────────────────
    //* ESECUZIONE QUERY E FETCH/FETCHALL 
    //───────────────────────────────────────────────────────────────
    #region STATMENT - EXECUTION - FETCH
    private function prepareAndExecute(): PDOStatement
    {
        // ritorno la generazione della stringa query con i parametri da bindare
        $query = $this->toSql();

        $stmt = $this->pdo->prepare($query);
        foreach ($this->bindings as $bind => $value) {
            $stmt->bindParam($bind, $value);
        }
        $stmt->execute();
        return  $stmt;
        // return $stmt->fetch($fetchTyep);
    }

    protected function fetch(int $fetchTyep = PDO::FETCH_ASSOC): array|object|bool
    {
        $stmt = $this->prepareAndExecute();
        if (!$stmt instanceof PDOStatement) {
            return false; // errore o query non eseguita
        }
        return $stmt->fetch($fetchTyep);
    }

    protected function fetchAll(int $fetchType = PDO::FETCH_ASSOC): array|object|bool
    {
        $stmt = $this->prepareAndExecute();
        if (!$stmt instanceof PDOStatement) {
            return false; // errore o query non eseguita
        }
        return $stmt->fetchAll($fetchType);
    }
}
