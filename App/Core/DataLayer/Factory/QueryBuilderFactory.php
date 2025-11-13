<?php

namespace App\Core\DataLayer\Factory;

use App\Core\DataLayer\Query\MySqlBuilder;
use App\Core\DataLayer\Query\PostgresQueryBuilder;
use App\Core\DataLayer\Query\QueryBuilder;
use InvalidArgumentException;

class QueryBuilderFactory
{
    public static function create(string $driveName){

        return match($driveName){
            'mysql' => new MySqlBuilder(),
            'postgres' => new PostgresQueryBuilder(),
            default => throw new InvalidArgumentException("Unsupported drive> $driveName"),
        };
    } 
}
