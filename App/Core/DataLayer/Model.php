<?php

declare(strict_types=1);

namespace App\Core\DataLayer;

use JsonSerializable;
use App\Core\Traits\Attributes;
use App\Core\Traits\StaticQueryMethods;
use App\Core\DataLayer\Query\ActiveQuery;

/**
 * @method static App\Core\Eloquent\Query\ActiveQuery select(array|string $columns)
 * @method static App\Core\Eloquent\Query\ActiveQuery create(array $data)
 * @method static App\Core\Eloquent\Query\ActiveQuery update(array $data)
 * @method static App\Core\Eloquent\Query\ActiveQuery where(string $columnName, string|int|float|bool|null $operatorOrValue, string|int|float|bool|null $value = null)
 * @method static App\Core\Eloquent\Query\ActiveQuery orderBy(array|string $columns, string $direction = 'ASC')
 * @method static App\Core\Eloquent\Query\ActiveQuery groupBy(string|array $columns)
 * @method static App\Core\Eloquent\Query\ActiveQuery get()
 * @method static App\Core\Eloquent\Query\ActiveQuery first()
 * @method static App\Core\Eloquent\Query\ActiveQuery find(int|string $id)
 * @method static App\Core\Eloquent\Query\ActiveQuery findOrFail(int|string $id)
 *
 * @see App\Core\Eloquent\Query\ActiveQuery
 */
class Model  implements JsonSerializable
{
    use Attributes;
    use StaticQueryMethods;
    

    public $primaryKey = 'id';
    protected string $table;
    protected array $fillable;

    protected bool $timestamps = true;

    public static function instance(): Model{
        return new static;
    }

    public function jsonSerialize(): mixed
    {
        return $this->attributes; // TODO da implementare
    }

    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(?string $key = null): mixed{
        if(is_null($key))
            return $this->attributes;
        else
            return $this->attributes[$key];
    }

    protected function setTimestamps(bool $bool): bool
    {
        return $this->timestamps = $bool;
    }

    protected function setTable(string $table): string
    {
        return $this->table = $table;
    }

    public function save(){
        $this->query()->save($this);
    }
      public static function findFirst(int|string $id): ?Model
    {
        return static::where(static::instance()->primaryKey, $id)->first();
    }

     public static function find(int|string $id)
    {
        return static::where(static::instance()->primaryKey, $id);
    }

    public function __toString(): string
    {
        return self::class;
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
