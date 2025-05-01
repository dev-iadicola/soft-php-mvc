<?php

namespace App\Core\Eloquent;

use Exception;
use PDO;
use App\Core\Database;
use App\Core\Eloquent\QueryBuilder;

class ORM extends Database
{
    protected string $table; // Nome della tabella
    protected array $fillable; // Campi riempibili
    public QueryBuilder $queryBuilder; // Query in costruzione

    public function __construct()
    {
        parent::__construct(); // Chiama il costruttore della classe padre per inizializzare la connessione al database
        // Inizializza la connessione al database
        $this->queryBuilder = new QueryBuilder(pdo: $this->pdo);
        $this->queryBuilder->setTable(table: $this->table);
        $this->queryBuilder->setFillable(fillable: $this->fillable);
    }
 

    public static function __callStatic($method, $parameters)
    {

        // Per ogni chiamata statica, creiamo un'istanza dell'ORM
        $instance = new static();
        // dd($instance);
        $queryBuilder = $instance->queryBuilder;  // Prendi l'istanza di QueryBuilder
        return call_user_func_array([$queryBuilder, $method], $parameters);  // Esegui il metodo su QueryBuilder

    }
}
