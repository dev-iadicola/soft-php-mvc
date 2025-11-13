<?php

namespace App\Core\DataLayer;


use Throwable;
use JsonSerializable;

use App\Core\Database;
use App\Traits\Attributes;
use App\Core\DataLayer\Query\ActiveQuery;
use App\Core\DataLayer\Runtime\OrmEngine;
use App\Core\DataLayer\Query\QueryBuilder;
use App\Core\Contract\QueryBuilderInterface;
use App\Core\Exception\QueryBuilderException;
use App\Core\Exception\ModelStructureException;
use App\Core\DataLayer\Schema\Validation\CheckSchema;

/**
 * @method static QueryBuilder select(array|string $columns)
 * @method static QueryBuilder where(string $columnName, string|int|float|bool|null $operatorOrValue, string|int|float|bool|null $value = null)
 * @method static QueryBuilder orderBy(array|string $columns, string $direction = 'ASC')
 * @method static QueryBuilder groupBy(string|array $columns)
 * @method static QueryBuilder get()
 * @method static QueryBuilder first()
 * @method static QueryBuilder find(int|string $id)
 * @method static QueryBuilder findOrFail(int|string $id)
 * @see QueryBuilder
 */
class Model implements JsonSerializable
{
    use Attributes;
    protected string $table; 
    protected array $fillable;
    private OrmEngine $orm;
    private QueryBuilderInterface $queryBuilder;


    public function checkTable()
    {
        if (!(getenv("APP_ENV") == 'testing' ||CheckSchema::tableExist($this->table))  )
            throw new ModelStructureException("Table {$this->table} Not Exist in Schema. Correct yout Model :  {$this->modelClass} or Schema");
         if (!$this->table) {
            $calledClass = get_class($this); // Ottieni il nome completo del Model
            throw new ModelStructureException("create variable table in : {$calledClass}");
        }
    }
    
    public static function Make(): ActiveQuery{
       
    
        $model = new static();

        $engine = $model->startEngine();
       
        return OrmEngine::Make( $engine->queryBuilder, $engine->queryExecutor, $engine->modelHydrator);
    }
    protected function startEngine(): OrmEngine
    {
        $this->checkTable();
        $this->orm = new OrmEngine(Database::getInstance()->getConnection());
        $this->orm->setModelClass(get_called_class());
        $this->orm->setTable(table: $this->table);
        $this->orm->setFillable(fillable: $this->fillable);
        return $this->orm;
    }

    


    
    public function jsonSerialize(): mixed
    {
        return $this->attributes; //TODO da implementare
    }


    /**
     * Summary of fill
     * clear the inputs with this function
     * @param array $values
     * @throws \App\Core\Exception\ModelStructureException
     * @return array
     */
    public function fill(array $values): array{
        if(property_exists($this, 'fillable')){
            throw new ModelStructureException("Model ". static::class . " must define the \$fillable property.");
         }
         if(empty($values)){
            return [];
         }
         // Filter the given array by allowed kleys
         $filtered = array_filter(
            $values,
            fn($key) => in_array($key, $this->fillable, true),
            ARRAY_FILTER_USE_KEY
         );
         // clean data.
         return $filtered;
    }
    
    public function setAttribute($key, $value){
        $this->attributes[$key] = $value;
    }

    /**
     | Handles static method calls on the Model and delegates them to the QueryBuilder.
     | and forwards them to the corresponding methods in {@see QueryBuilder}.
     __________________________________________________________________________
     * @param string $method     The name of the called static method.
     * @param array  $parameters The parameters passed to the method.
     * @see QueryBuilder
     * @throws \App\Core\Exception\QueryBuilderException
     * @return mixed The result of the executed QueryBuilder method.
     */
    public function __call($method, $parameters)
    {
        // Se il metodo è definito nel Model (non statico)
        if (method_exists($this, $method)) {
            return $this->$method(...$parameters);
        }


         $instance = new static();
        $activeQuery = $instance::Make();
        if ($activeQuery && method_exists($activeQuery, $method)) {
            return $activeQuery->$method(...$parameters);
        }

        throw new \BadMethodCallException("Metodo {$method} not foun in Model " . static::class);
    }

    public function setQueryBuilder(QueryBuilderInterface $qb){
        $this->queryBuilder = $qb;
    }

    public static function __callStatic($method, $parameters)
    {

        // If the static method is defined in the subclass (e.g. LogTrace::createLog)       
        if (method_exists(static::class, $method)) {
            // Usa forward_static_call_array per chiamarlo in modo pulito e statico
            return forward_static_call_array([static::class, $method], $parameters);
        }

        $instance = new static();
        $activeQuery = $instance::Make();
      
        if (method_exists($activeQuery, $method)) {
            return $activeQuery->$method(...$parameters);
        }
        
        try {
            if ($activeQuery && (method_exists($activeQuery, $method))) {
                return $activeQuery->$method(...$parameters);
            }
            
        } catch (QueryBuilderException $e) {
            $instance->throwHere(e: $e);
        }
    }


    private static function throwHere(Throwable $e): never{
        // Get complete  backtrace 
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        // Trova il primo frame che NON è interno al core
        $caller = null;
        foreach ($trace as $frame) {
            if (
                isset($frame['file']) &&
                !str_contains($frame['file'], 'App/Core/DataLayer') &&
                !str_contains($frame['file'], 'call_user_func_array')
            ) {
                $caller = $frame;
                break;
            }
        }

        // Prepara il testo d’origine solo se i dati sono disponibili
        $origin = '';
        if ($caller && isset($caller['file'], $caller['line'])) {
            $origin = " (from {$caller['file']} line {$caller['line']})";
        }

        // Ri-lancia l’eccezione con contesto
        throw new QueryBuilderException($e->getMessage() . $origin);
    }
}
