<?php

namespace App\Core\Eloquent\Query;


use App\Core\Contract\QueryBuilderInterface;
use App\Core\Exception\QueryBuilderException;
use App\Traits\Attributes;
use PDO;
use App\Core\Exception\ModelNotFoundException;
use App\Core\Exception\ModelStructureException;
use App\Core\Eloquent\Schema\Validation\CheckSchema;
use App\Utils\Enviroment;
use PDOStatement;

abstract class AbstractBuilder implements QueryBuilderInterface
{
    protected array $fillable = []; // Attributi che possono essere assegnati in massa
    // Binding params
    protected array $bindings = []; 

    protected array $systemColumns = ['id', 'created_at', 'updated_at'];

    public int|float|string|null $id = null; // ID dell'istanza
    protected int $paramCounter = 0;

    // Cluse for QB
    protected ?string $table = null;
    protected string $insertClause ='';
    protected string $setClause = '';
    protected string $whereClause ='';

    public function toUpdate(): string
    {
        if (empty($this->table)) {
            throw new QueryBuilderException("No table defined for UPDATE");
        }

        if (empty($this->setClause)) {
            throw new QueryBuilderException("No SET clause defined for UPDATE");
        }

        $table = str_replace('FROM ', '', $this->table);

        return trim("UPDATE $table SET {$this->setClause} {$this->whereClause};");
    }

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

    public function getFillable(): array
    {
        return $this->fillable;
    }

    public function fill(array $values): array
    {
        $fillable = $this->fillable;

        // Filter the values to keep only those defined in $fillable
        return $filteredValues = array_filter(
            $values,
            fn($key) => in_array($key, $fillable),
            ARRAY_FILTER_USE_KEY
        );
    }






    
    public function setTable(string $table): void
    {
        $this->table = $table;
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

    public function setBinding(string $key, $value): string
    {
        return $this->bindings[$key] = $value;
    }

    // * ___________________________________________________
    #region GETTER 
    // * ___________________________________________________
   
    public function getNameTable(): string|null
    {
        return $this->table;
    }


     // * ___________________________________________________
    #region BINDING     
    // * ___________________________________________________
    public function getBindings(?string $key = null): array|string
    {
        return is_null($key) ? $this->bindings : $this->bindings[$key];
    }

    protected function AddBind(?string $val): mixed
    {
        $val = $this->processingNull($val);
        $key = ":p_" . ++$this->paramCounter;
        $this->bindings[":p_" . $this->paramCounter] = $val;
        return $key;
    }

    private function processingNull($value): mixed
    {
        if (is_null($value))
            return " IS NULL ";
        else
            return $value;
    }

    #region To QUERY 
    public function toInsert(): string
    {
        if (empty($this->setClause)) {
            throw new QueryBuilderException("No INSERT statement defined");
        }

        return $this->setClause . ';';
    }

    public function toDelete(): string{
        if (is_null($this)) {
            throw new ModelStructureException("Missing table name definition in the model. Please define a protected \$table property.");
        }
        
        if (empty($this->whereClause)) {
            throw new QueryBuilderException("Cannot execute operation: missing WHERE clause. Use where() before performing this query.");
        }
        
        return "DELETE FROM {$this->table} $this->whereClause";
    }
}
