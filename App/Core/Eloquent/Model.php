<?php

namespace App\Core\Eloquent;


use App\Core\Exception\ModelStructureException;
use JsonSerializable;
use App\Core\Database;
use App\Core\Eloquent\QueryBuilder;

class Model implements JsonSerializable
{
    protected string $table; // Nome della tabella
    protected array $fillable; // Campi riempibili
    private QueryBuilder $queryBuilder; // Permettendo di ereditare i suoi metodi, costruisce la query

    protected function boot(){

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
        return call_user_func_array([$queryBuilder, $method], $parameters);  // Esegui il metodo su QueryBuilder con tutte le proprietà del Model

    }

    public function jsonSerialize(): mixed{
        return $this->toArray(); //TODO da implementare
    }

    public function getTable(){
        return $this->table;
    }
}
