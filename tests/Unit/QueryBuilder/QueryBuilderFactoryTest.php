<?php

declare(strict_types=1);

namespace Tests\Unit\QueryBuilder;

use App\Core\DataLayer\Factory\QueryBuilderFactory;
use App\Core\DataLayer\Query\MySqlBuilder;
use App\Core\DataLayer\Query\PostgresQueryBuilder;
use App\Core\DataLayer\Query\SqliteQueryBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class QueryBuilderFactoryTest extends TestCase
{
    public function testCreatesMysqlBuilderForMysqlDriver(): void
    {
        $builder = QueryBuilderFactory::create('mysql');

        $this->assertInstanceOf(MySqlBuilder::class, $builder);
        $this->assertNotInstanceOf(SqliteQueryBuilder::class, $builder);
    }

    public function testCreatesSqliteBuilderForSqliteDriver(): void
    {
        $builder = QueryBuilderFactory::create('sqlite');

        $this->assertInstanceOf(SqliteQueryBuilder::class, $builder);
    }

    public function testCreatesPostgresBuilderForPostgresDriver(): void
    {
        $builder = QueryBuilderFactory::create('postgres');

        $this->assertInstanceOf(PostgresQueryBuilder::class, $builder);
    }

    public function testThrowsForUnsupportedDriver(): void
    {
        $this->expectException(InvalidArgumentException::class);

        QueryBuilderFactory::create('sqlserver');
    }
}
