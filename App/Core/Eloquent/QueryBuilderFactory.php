<?php

namespace App\Core\Eloquent;

use App\Core\Eloquent\Query\MySqlBuilder;
use App\Core\Eloquent\Query\PostgresQueryBuilder;
use App\Core\Eloquent\Query\QueryBuilder;
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
