<?php

namespace App\Core\Eloquent;


use JsonSerializable;
use App\Core\Database;
use App\Core\Eloquent\QueryBuilder;
use App\Core\Exception\QueryBuilderException;
use App\Core\Exception\ModelStructureException;

class Model implements JsonSerializable
{
    protected string $table; // Nome della tabella
    protected array $fillable; // Campi riempibili
    private QueryBuilder $queryBuilder; // Permettendo di ereditare i suoi metodi, costruisce la query

    protected function boot()
    {

        if (!$this->table) {
            $calledClass = get_class($this); // Ottieni il nome completo del Model
            throw new ModelStructureException("La proprietà 'table' non è definita nel model: {$calledClass}");
        }

        $this->queryBuilder = new QueryBuilder();
        $this->queryBuilder->setPDO(Database::getInstance()->getConnection());
        $this->queryBuilder->setClassModel(get_called_class());
        $this->queryBuilder->setTable(table: $this->table);
        $this->queryBuilder->setFillable(fillable: $this->fillable);
    }



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
