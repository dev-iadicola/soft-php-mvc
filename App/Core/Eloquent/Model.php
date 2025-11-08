<?php

namespace App\Core\Eloquent;


use App\Traits\Attributes;
use App\Traits\Getter;
use App\Traits\Setter;
use JsonSerializable;
use App\Core\Database;
use App\Core\Eloquent\QueryBuilder;
use App\Core\Exception\QueryBuilderException;
use App\Core\Exception\ModelStructureException;
use RuntimeException;

/**
 * Classe base per tutti i Model del framework.
 *
 * Fornisce un'interfaccia statica verso {@see QueryBuilder} tramite il metodo magico {@see Model::__callStatic()}.
 *
 * I metodi statici come `where()`, `orderBy()`, `get()`, ecc.
 * vengono automaticamente inoltrati a una nuova istanza di QueryBuilder
 * configurata in base al Model corrente.
 *
 * Esempio:
 * ```php
 * $profiles = Profile::where('selected', true)
 *                    ->orderBy('id', 'DESC')
 *                    ->get();
 * ```
 *
 * @method static QueryBuilder select(array|string $columns)
 * @method static QueryBuilder where(string $columnName, string|int|float|bool|null $operatorOrValue, string|int|float|bool|null $value = null)
 * @method static QueryBuilder orderBy(array|string $columns, string $direction = 'ASC')
 * @method static QueryBuilder groupBy(string|array $columns)
 * @method static QueryBuilder get()
 * @method static QueryBuilder first()
 * @method static QueryBuilder find(int|string $id)
 * @method static QueryBuilder findOrFail(int|string $id)
 *
 * @see QueryBuilder
 */
class Model implements JsonSerializable
{
    use Attributes;
    protected string $table; // Nome della tabella
    protected array $fillable; // Campi riempibili
    private ?QueryBuilder $queryBuilder = null; // Permettendo di ereditare i suoi metodi, costruisce la query


    protected function boot(): void
    {

        if (!$this->table) {
            $calledClass = get_class($this); // Ottieni il nome completo del Model
            throw new ModelStructureException("create variable table in : {$calledClass}");
        }

        $this->queryBuilder = new QueryBuilder();
        $this->queryBuilder->setPDO(Database::getInstance()->getConnection());
        $this->queryBuilder->setClassModel(get_called_class());
        $this->queryBuilder->setTable(table: $this->table);
        $this->queryBuilder->setFillable(fillable: $this->fillable);
        // TODO: vedere se implementare oppure no Instance::context
        //Instance::context(builder: $this->queryBuilder); 
    }

    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function save(): bool|QueryBuilder
    {
        if (!$this->queryBuilder) {
            throw new RuntimeException("Querybuilder not connected to Model");
        }
        // recover data to save
        $data = $this->attributes;

        // Filter with fillable
        $this->fillable = $this->queryBuilder->getFillable();
        $data = array_filter(
            $data,
            fn($key) => in_array($key, $this->fillable),
            ARRAY_FILTER_USE_KEY
        );
        // if the key id exist, update the record.
        if (isset($this->attributes['id'])) {
            return $this->queryBuilder->where('id', $this->attributes['id'])->update($data);
        }
        // else, create e new record in DB
        return $this->queryBuilder->create($data);
    }

  



    /**
     * Gestisce le chiamate statiche ai metodi del Model e le inoltra al QueryBuilder.
     *
     * Questo metodo intercetta tutte le chiamate statiche come:
     * ```php
     * User::where('id', 1)->first();
     * ```
     * e le delega ai metodi equivalenti definiti in {@see QueryBuilder}.
     *
     * @param string $method     Nome del metodo chiamato staticamente.
     * @param array  $parameters Parametri passati al metodo.
     *
     * @see QueryBuilder
     * @throws \App\Core\Exception\QueryBuilderException
     * @return mixed Il risultato del metodo chiamato sul QueryBuilder.
     */


    public function __call($method, $parameters)
    {
        // Se il metodo è definito nel Model (non statico)
        if (method_exists($this, $method)) {
            return $this->$method(...$parameters);
        }

        // Altrimenti, fallback al QueryBuilder
        $builder = $this->queryBuilder ?? $this->boot();
        if ($builder && method_exists($builder, $method)) {
            return $builder->$method(...$parameters);
        }

        throw new \BadMethodCallException("Metodo {$method} non trovato nel Model " . static::class);
    }

    public static function __callStatic($method, $parameters)
    {

    // If the static method is defined in the subclass (e.g. LogTrace::createLog)       
        if (method_exists(static::class, $method)) {
            // Usa forward_static_call_array per chiamarlo in modo pulito e statico
            return forward_static_call_array([static::class, $method], $parameters);
        }

        // Per ogni chiamata statica, creiamo un'istanza dell'Model
        $instance = new static();


        // Se il metodo esiste nell'istanza (es. non statico del Model)
        if (method_exists($instance, $method)) {
            return $instance->$method(...$parameters);
        }


        $instance->boot();
        $queryBuilder = $instance->queryBuilder;  // Prendi l'istanza di QueryBuilder
        try {
            if ($queryBuilder && method_exists($queryBuilder, $method)) {
                return call_user_func_array(callback: [$queryBuilder, $method], args: $parameters);  // Esegui il metodo su QueryBuilder con tutte le proprietà del Model
            }
        } catch (QueryBuilderException $e) {
            // Ottieni il backtrace completo
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            // Trova il primo frame che NON è interno al core
            $caller = null;
            foreach ($trace as $frame) {
                if (
                    isset($frame['file']) &&
                    !str_contains($frame['file'], 'App/Core/Eloquent') &&
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




    public function jsonSerialize(): mixed
    {
        return $this->attributes; //TODO da implementare
    }

    public function getTable()
    {
        return $this->table;
    }
}
