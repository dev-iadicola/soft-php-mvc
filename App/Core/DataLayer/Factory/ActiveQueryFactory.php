<?php

namespace App\Core\DataLayer\Factory;

use App\Core\DataLayer\Query\ActiveQuery;
use App\Core\DataLayer\Query\ModelHydrator;
use App\Core\DataLayer\Query\QueryExecutor;
use App\Core\DataLayer\Runtime\ORM;
use App\Core\Exception\ModelStructureException;
/**
 * ActiveQueryFactory
 *
 * Builds a complete ActiveQuery pipeline for a specific model.
 * This is the correct way to instantiate:
 *
 * - QueryBuilder
 * - QueryExecutor
 * - ModelHydrator
 *
 * using the global ORM runtime (PDO + driver).
 */
class ActiveQueryFactory
{
    public static function for(string $modelClass){
        $model = new $modelClass;

        // metadata
        $table = $model->table ?? throw new ModelStructureException("Missing table in: $modelClass");
        $fillable = $model->fillable ?? [];


        // Runtime (global)
        $pdo = ORM::getPDO();
        $driver = ORM::getDrive();

        // Build components
        $builder =  QueryBuilderFactory::create($driver);
        $executor = new QueryExecutor($pdo);
        $hydrator = new ModelHydrator($builder);
        $hydrator->setModelClass($modelClass);
        

        // config builder
        $builder->from($table);
        $builder->setFillable($fillable);
       

        // Run ActiveQuery instance 

        return new ActiveQuery(
            $builder,
            $executor,
            $hydrator
        );


        
    }
}
