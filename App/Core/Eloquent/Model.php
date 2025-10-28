<?php

namespace App\Core\Eloquent;


use JsonSerializable;
use App\Core\Database;
use App\Core\Eloquent\QueryBuilder;
use App\Core\Exception\QueryBuilderException;
use App\Core\Exception\ModelStructureException;
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
    protected string $table; // Nome della tabella
    protected array $fillable; // Campi riempibili
    private QueryBuilder $queryBuilder; // Permettendo di ereditare i suoi metodi, costruisce la query

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
        Instance::context(builder: $this->queryBuilder);
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
    public static function __callStatic($method, $parameters)
    {
        // Per ogni chiamata statica, creiamo un'istanza dell'Model
        $instance = new static();
        $instance->boot();
        $queryBuilder = $instance->queryBuilder;  // Prendi l'istanza di QueryBuilder
        try {
            return call_user_func_array([$queryBuilder, $method], args: $parameters);  // Esegui il metodo su QueryBuilder con tutte le proprietà del Model

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
        return $this->toArray(); //TODO da implementare
    }

    public function getTable()
    {
        return $this->table;
    }
}
