<?php

declare(strict_types=1);

namespace App\Core\DataLayer\Factory;

use App\Core\DataLayer\Query\AbstractBuilder;
use App\Core\DataLayer\Query\MySqlBuilder;
use App\Core\DataLayer\Query\PostgresQueryBuilder;
use App\Core\DataLayer\Query\SqliteQueryBuilder;
use InvalidArgumentException;

class QueryBuilderFactory
{
    public static function create(string $driveName): AbstractBuilder{

        return match($driveName){
            'mysql' => new MySqlBuilder(),
            'sqlite' => new SqliteQueryBuilder(),
            'postgres' => new PostgresQueryBuilder(),
            default => throw new InvalidArgumentException("Unsupported driver: $driveName"),
        };
    } 
}
