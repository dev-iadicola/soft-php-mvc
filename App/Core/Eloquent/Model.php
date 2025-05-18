<?php

namespace App\Core\Eloquent;


use App\Core\Exception\ModelStructureException;
use JsonSerializable;
use App\Core\Database;
use App\Core\Eloquent\QueryBuilder;

class Model extends Database implements JsonSerializable
{
    protected string $table; // Nome della tabella
    protected array $fillable; // Campi riempibili
    public QueryBuilder $queryBuilder; // Query in costruzione

    public function __construct()
    {
        parent::__construct(); // Chiama il costruttore della classe padre per inizializzare la connessione al database
        
    }
 

    protected function boot(){

        if (!$this->table) {
            $calledClass = get_class($this); // Ottieni il nome completo del Model
            throw new ModelStructureException("La proprietà 'table' non è definita nel model: {$calledClass}");
        }
        $this->queryBuilder = new QueryBuilder(pdo: $this->pdo);
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
        return $this->toArray(); // da implementare
    }

    public function getTable(){
        return $this->table;
    }
}
