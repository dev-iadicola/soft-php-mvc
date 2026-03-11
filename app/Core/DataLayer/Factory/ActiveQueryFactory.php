<?php

declare(strict_types=1);

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
    public static function for(string $modelClass): ActiveQuery{
        $model = new $modelClass;

        // Read ORM metadata directly from the model's declared structure.
        $table = $model->getTable() ?: throw new ModelStructureException("Missing table in: $modelClass");
        $allowedColumns = $model->columns();
        $timestampsExists = $model->timestamps ?? true;


        // Runtime (global)
        $pdo = ORM::getPDO();
        $driver = ORM::getDriver();

        // Build components
        $builder =  QueryBuilderFactory::create($driver);
        $executor = new QueryExecutor($pdo);
        $hydrator = new ModelHydrator($builder);
        $hydrator->setModelClass($modelClass);
        

        // Configure the builder so mass assignment follows the typed model properties.
        $builder->from($table);
        $builder->setAllowedColumns($allowedColumns);
        $builder->timestampsExists($timestampsExists);
       

        // Run ActiveQuery instance 

        return new ActiveQuery(
            $builder,
            $executor,
            $hydrator
        );


        
    }
}
