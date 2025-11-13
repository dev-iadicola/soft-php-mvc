<?php

namespace App\Core\DataLayer;


use App\Core\Traits\StaticQueryMethods;
use JsonSerializable;
use App\Traits\Attributes;


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
    use Attributes; use StaticQueryMethods;
    protected string $table;
    protected array $fillable;


    public function jsonSerialize(): mixed
    {
        return $this->attributes; //TODO da implementare
    }
    public function setAttribute($key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    // /**
    //  | Handles static method calls on the Model and delegates them to the QueryBuilder.
    //  | and forwards them to the corresponding methods in {@see QueryBuilder}.
    //  __________________________________________________________________________
    //  * @param string $method     The name of the called static method.
    //  * @param array  $parameters The parameters passed to the method.
    //  * @see App\Core\Eloquent\Query\ActiveQuery
    //  * @throws \App\Core\Exception\QueryBuilderException
    //  * @return mixed The result of the executed QueryBuilder method.
    //  */
    // public function __call($method, $parameters)
    // {
    //     // Se il metodo è definito nel Model (non statico)
    //     if (method_exists($this, $method)) {
    //         return $this->$method(...$parameters);
    //     } else if (isset($this->fillable[$method])) {
    //         return $this->fillable[$method];
    //     } else if (method_exists(ActiveQuery::class, $method)) {
    //         return ActiveQueryFactory::for(static::class)->$method;
    //     }

    //     throw new \BadMethodCallException("Metodo {$method} not foun in Model " . static::class);
    // }

    // public function setQueryBuilder(ActiveQuery $qb)
    // {
    //     $this->queryBuilder = $qb;
    // }

    // public static function __callStatic($method, $parameters)
    // {

    //     // If the static method is defined in the subclass (e.g. LogTrace::createLog)       
    //     if (method_exists(static::class, $method)) {
    //         // Usa forward_static_call_array per chiamarlo in modo pulito e statico
    //         return forward_static_call_array([static::class, $method], $parameters);
    //     } else if (method_exists(ActiveQuery::class, $method)) {
    //         try {
    //             return ActiveQueryFactory::for(static::class)->{$method};

    //         }catch(QueryBuilderException $e){
    //                 $model = new static;
    //                 $model->throwHere($e);
    //         }
    //     }


      
    // }


    // private static function throwHere(Throwable $e): never
    // {
    //     // Get complete  backtrace 
    //     $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    //     // Trova il primo frame che NON è interno al core
    //     $caller = null;
    //     foreach ($trace as $frame) {
    //         if (
    //             isset($frame['file']) &&
    //             !str_contains($frame['file'], 'App/Core/DataLayer') &&
    //             !str_contains($frame['file'], 'call_user_func_array')
    //         ) {
    //             $caller = $frame;
    //             break;
    //         }
    //     }

    //     // Prepara il testo d’origine solo se i dati sono disponibili
    //     $origin = '';
    //     if ($caller && isset($caller['file'], $caller['line'])) {
    //         $origin = " (from {$caller['file']} line {$caller['line']})";
    //     }

    //     // Ri-lancia l’eccezione con contesto
    //     throw new QueryBuilderException($e->getMessage() . $origin);
    // }
}
