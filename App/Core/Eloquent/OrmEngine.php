<?php

namespace App\Core\Eloquent;

use PDO;
use App\Core\Eloquent\Query\ActiveQuery;
use App\Core\Eloquent\Query\Transaction;
use App\Core\Eloquent\Query\ModelHydrator;
use App\Core\Eloquent\Query\QueryExecutor;
use App\Core\Contract\QueryBuilderInterface;
use App\Core\Exception\QueryBuilderException;
use App\Core\Exception\ModelStructureException;
use App\Core\Mvc;
use App\Utils\Enviroment;

/**
 * Class OrmEngine
 * ________________________________________
 *
 * @package App\Core\Eloquent
 */
class OrmEngine
{

    use Transaction;


    private PDO $pdo;

    private ?string $modelClass = null;
    public QueryExecutor $queryExecutor;
    public QueryBuilderInterface $queryBuilder;

    public ModelHydrator $modelHydrator;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $facotry = QueryBuilderFactory::create(Mvc::$mvc->config->settings['db']['driver']);
        $this->queryBuilder = $facotry;
        $this->queryExecutor = new QueryExecutor($pdo);
        $this->modelHydrator = new ModelHydrator($this->queryBuilder);
    }

    public static function Make(
        QueryBuilderInterface $builder,
        QueryExecutor $executor,
        ModelHydrator $hydrator,
    ): ActiveQuery {

        return new ActiveQuery(
            $builder,
            $executor,
            $hydrator
        );
    }


    /**
     * Query Buiilder 
     */
    #region Query



   



    


 
   

    //     /**
//      * SETTER
//      */

    public function setModelClass(string $modelClass)
    {
        $this->modelHydrator->setModelClass($modelClass);
    }
    public function setTable(string $table): void
    {
        $table = trim($table);
        if (empty($table))
            throw new ModelStructureException("Table name cannot be empy. Please, define a valid table name in  {$this->modelHydrator}");
        $this->queryBuilder->from($table);
    }
    public function setFillable(array $fillable): void
    {
        $this->queryBuilder->setFillable($fillable);
    }


    /**
     * Getter
     */
    public function getQueryBuilder(): QueryBuilderInterface
    {
        return $this->queryBuilder;
    }









    /* ───────────────────────────────────────────────────────────────
       #region GETTER
       //* ───────────────────────────────────────────────────────────────
       /**
        * Summary of get
        * @param int $fetchType
        * @throws \App\Core\Exception\ModelNotFoundException
        * @return array<Model>
        */
    

 
    /**
     * Summary of findAll
     * @param int $fetchType
     * @throws \Exception
     * @return array<Model>
     */
   



    // // TODO: not conluded, to do finished process for method update 
    // public function update(array $data): self
    // {
    //     $this->queryBuilder->set($data);


    //     $this->getKeyId();


    //     $id = $this->queryBuilder->id;
    //     $whereClause = $this->queryBuilder->where('id', $id) ?: "WHERE id = :id";
    //     $result = $this->queryBuilder->getBindings(':id');
    //     if (!isset($result) && !$this->queryBuilder->whereClause) {
    //         $this->queryBuilder->setBinding(':id', $this->getKeyId());
    //     }

    //     $query = "UPDATE {$this->queryBuilder->getNameTable()} SET {$setClause} {$whereClause}";
    //     $stmt = $this->pdo->prepare($query);


    //     // Bind dei valori da aggiornare
    //     foreach ($values as $key => $val) {
    //         // Fixed for php 8.4. (verifica che sia una stringa prima di fare null)
    //         if (is_string($val) && trim($val) === '') {
    //             $val = NULL;
    //         }
    //         $stmt->bindValue(":$key", $val);
    //     }

    //     // Bind dei valori nella WHERE
    //     foreach ($this->queryBuilder->getBindings() as $param => $value) {
    //         if (!isset($values[ltrim($param, ':')])) {
    //             $stmt->bindValue($param, $value);
    //         }
    //     }

    //     $stmt->execute();
    //     return $this;
    // }

    
    




    // public function save(): bool|QueryBuilder
    // {
    //     // recover data to save
    //     $data = $this->attributes;
    //     // Filter with fillable
    //     $this->fillable = $this->getFillable();
    //     $data = array_filter(
    //         $data,
    //         fn($key) => in_array($key, $this->fillable),
    //         ARRAY_FILTER_USE_KEY
    //     );
    //     // if the key id exist, update the record.
    //     if (isset($this->attributes['id'])) {
    //         return $this->where('id', $this->attributes['id'])->update($data);
    //     }
    //     // else, create e new record in DB
    //     return $this->create($data);
    // }

    // public function save(array $values): bool
    // {
    //     if (empty($this->table)) {
    //         throw new ModelStructureException("Table name hasn't been set in Model " . $this->modelClass);
    //     }
    //     $fillable = $this->fillable;
    //     if (!empty($fillable)) {
    //         $values = array_filter($values, fn($key) => in_array($key, $fillable), ARRAY_FILTER_USE_KEY);
    //     }

    //     $values = array_map(fn($value) => self::removeSpecialChars($value), $values);
    //     $keys = array_keys($values);
    //     $fields = implode(',', $keys);
    //     $placeholders = implode(',', array_map(fn($key) => ":$key", $keys));

    //     $query = "INSERT INTO $this->table ($fields) VALUES ($placeholders)";
    //     $stmt = $this->pdo->prepare($query);

    //     foreach ($values as $field => $fieldValue) {
    //         $stmt->bindValue(":$field", $fieldValue);
    //     }

    //     return $stmt->execute();
    // }

    //* ───────────────────────────────────────────────────────────────
    #region UTILS
    //* ───────────────────────────────────────────────────────────────
    public static function removeSpecialChars(string $value): string
    {
        return htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');
    }


    /**
     * Valida che le colonne passate a metodi come orderBy() o groupBy() siano sicure.
     * 
     * Determina automaticamente il nome del metodo chiamante tramite debug_backtrace()
     * per restituire messaggi d’errore più precisi.
     *
     * @param array|string $columns  Colonne da validare
     * @param bool $allowMultiple    Permette più colonne (true per groupBy)
     * @return array                 Array di colonne validate
     * @deprecated 
     * @throws QueryBuilderException Se una colonna non è ammessa
     */
    protected function validateColumns(array|string $columns, bool $allowMultiple = false): array
    {
        // Ottieni il backtrace completo per risalire al chiamante reale
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $caller = null;

        // Trova il primo frame che non appartiene al core del framework
        foreach ($trace as $frame) {
            if (isset($frame['file']) && !str_contains($frame['file'], 'App/Core/Eloquent')) {
                $caller = $frame;
                break;
            }
        }

        // Determina il nome della funzione chiamante e l’origine del file
        $callerName = $trace[1]['function'] ?? 'unknown';
        $originInfo = $caller
            ? " (called from {$caller['file']} on line {$caller['line']})"
            : '';

        // Colonne consentite (fillable + systemColumns)
        $allowed = array_merge($this->fillable, $this->systemColumns);
        $validated = [];

        // Normalizza: accetta stringa singola o array
        if (is_string($columns)) {
            $columns = [$columns];
        }

        foreach ($columns as $col) {
            $col = trim($col);

            // Blocca SQL injection tramite colonne arbitrarie
            if (!in_array($col, $allowed, true)) {
                throw new QueryBuilderException(
                    "Invalid column '{$col}' passed to {$callerName}() in model {$this->modelClass}{$originInfo}"
                );
            }

            $validated[] = $col;
        }

        // Evita che vengano passate più colonne se non consentito
        if (!$allowMultiple && count($validated) > 1) {
            throw new QueryBuilderException(
                "Multiple columns are not allowed in {$callerName}() in model {$this->modelClass}{$originInfo}"
            );
        }

        return $validated;
    }
    public function toArray(): array
    {
        return array_map(fn($m) => get_object_vars($m), $this->get());
    }


    //───────────────────────────────────────────────────────────────
    #region CLONE    
    //───────────────────────────────────────────────────────────────
    public function duplicate(): static
    {
        return clone $this;
    }

    /**
     * OPERATION SQL
     */
    public function count(string $column = '*'): int|false
    {
        $this->queryBuilder->selectValues = " COUNT({$column}) AS count";
        return $this->queryExecutor->fetchColumn($this->queryBuilder->toSql(), $this->queryBuilder->getBindings());
    }

    public function max(string $column): int|false
    {
        $this->queryBuilder->selectValues = " MAX({$column})";
        return $this->queryExecutor->fetchColumn($this->queryBuilder->toSql(), $this->queryBuilder->getBindings());
    }

    public function min(string $column): int|false
    {
        $this->queryBuilder->selectValues = " MIN({$column})";
        return $this->queryExecutor->fetchColumn($this->queryBuilder->toSql(), $this->queryBuilder->getBindings());
    }

    public function countDistinct(string $column): int|false
    {
        $this->queryBuilder->selectValues = " COUNT(DISTINCT {$column}) ";
        return $this->queryExecutor->fetchColumn($this->queryBuilder->toSql(), $this->queryBuilder->getBindings());
    }
    public function sum(string $column): int|false
    {
        $this->queryBuilder->selectValues = " SUM({$column}) ";
        return $this->queryExecutor->fetchColumn($this->queryBuilder->toSql(), $this->queryBuilder->getBindings());
    }
    public function sumDistinct(string $column): int|false
    {
        $this->queryBuilder->selectValues = " SUM( DISTINCT {$column}) ";
        return $this->queryExecutor->fetchColumn($this->queryBuilder->toSql(), $this->queryBuilder->getBindings());
    }
    public function avg(string $column): int|false
    {
        $this->queryBuilder->selectValues = " AVG({$column}) ";
        return $this->queryExecutor->fetchColumn($this->queryBuilder->toSql(), $this->queryBuilder->getBindings());
    }

    public function avgDistinct(string $column): int|false
    {
        $this->queryBuilder->selectValues = " AVG(DISTINCT {$column}) ";
        return $this->queryExecutor->fetchColumn($this->queryBuilder->toSql(), $this->queryBuilder->getBindings());
    }
}
