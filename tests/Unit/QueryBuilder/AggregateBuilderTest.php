<?php

declare(strict_types=1);

namespace Tests\Unit\QueryBuilder;

use App\Core\DataLayer\Query\MySqlBuilder;
use App\Core\Exception\QueryBuilderException;
use PHPUnit\Framework\TestCase;

class AggregateBuilderTest extends TestCase
{
    private MySqlBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new MySqlBuilder();
        $this->builder->from('logs');
    }

    public function testSelectRawReplacesWildcard(): void
    {
        $this->builder->selectRaw('COUNT(*) AS total');
        $sql = $this->builder->toSql();

        $this->assertStringContainsString('SELECT COUNT(*) AS total', $sql);
        $this->assertStringNotContainsString('*,', $sql);
    }

    public function testSelectRawAppendsToExistingSelect(): void
    {
        $this->builder->select('name');
        $this->builder->selectRaw('COUNT(*) AS total');
        $sql = $this->builder->toSql();

        $this->assertStringContainsString('SELECT name, COUNT(*) AS total', $sql);
    }

    public function testSelectAggregateWithAlias(): void
    {
        $this->builder->selectAggregate('COUNT', '*', 'login_count');
        $sql = $this->builder->toSql();

        $this->assertStringContainsString('COUNT(*) AS login_count', $sql);
    }

    public function testSelectAggregateWithoutAlias(): void
    {
        $this->builder->selectAggregate('MAX', 'last_log');
        $sql = $this->builder->toSql();

        $this->assertStringContainsString('MAX(last_log)', $sql);
    }

    public function testSelectAggregateRejectsInvalidFunction(): void
    {
        $this->expectException(QueryBuilderException::class);
        $this->builder->selectAggregate('INVALID', 'col');
    }

    public function testSelectAggregateCaseInsensitive(): void
    {
        $this->builder->selectAggregate('sum', 'amount', 'total');
        $sql = $this->builder->toSql();

        $this->assertStringContainsString('SUM(amount) AS total', $sql);
    }

    public function testMultipleSelectAggregates(): void
    {
        $this->builder->select('device');
        $this->builder->selectAggregate('COUNT', '*', 'cnt');
        $this->builder->selectAggregate('MAX', 'last_log', 'latest');
        $sql = $this->builder->toSql();

        $this->assertStringContainsString('device, COUNT(*) AS cnt, MAX(last_log) AS latest', $sql);
    }

    public function testToAggregateWithWhere(): void
    {
        $this->builder->where('device', 'Chrome');
        $sql = $this->builder->toAggregate('COUNT');

        $this->assertStringContainsString('SELECT COUNT(*)', $sql);
        $this->assertStringContainsString('FROM logs', $sql);
        $this->assertStringContainsString('WHERE device =', $sql);
    }

    public function testToAggregateWithColumn(): void
    {
        $sql = $this->builder->toAggregate('MAX', 'last_log');

        $this->assertStringContainsString('SELECT MAX(last_log)', $sql);
        $this->assertStringContainsString('FROM logs', $sql);
    }

    public function testGroupByRaw(): void
    {
        $this->builder->select('device');
        $this->builder->groupByRaw('device, user_id');
        $sql = $this->builder->toSql();

        $this->assertStringContainsString('GROUP BY device, user_id', $sql);
    }

    public function testGroupByRawCannotBeCalledTwice(): void
    {
        $this->builder->groupByRaw('device');

        $this->expectException(QueryBuilderException::class);
        $this->builder->groupByRaw('user_id');
    }

    public function testGroupByAndGroupByRawConflict(): void
    {
        $this->builder->groupBy('device');

        $this->expectException(QueryBuilderException::class);
        $this->builder->groupByRaw('user_id');
    }

    public function testHavingRaw(): void
    {
        $this->builder->select('device');
        $this->builder->selectRaw('COUNT(*) AS cnt');
        $this->builder->groupBy('device');
        $this->builder->havingRaw('COUNT(*) > 5');
        $sql = $this->builder->toSql();

        $this->assertStringContainsString('HAVING COUNT(*) > 5', $sql);
    }

    public function testFullAggregateQueryBuild(): void
    {
        $this->builder->select(['indirizzo', 'device']);
        $this->builder->selectAggregate('COUNT', '*', 'login_count');
        $this->builder->selectAggregate('MAX', 'last_log', 'last_log');
        $this->builder->where('user_id', 1);
        $this->builder->groupBy(['indirizzo', 'device']);
        $this->builder->orderBy('last_log', 'DESC');
        $this->builder->limit(10);
        $this->builder->offset(0);

        $sql = $this->builder->toSql();

        $this->assertStringContainsString('SELECT indirizzo, device, COUNT(*) AS login_count, MAX(last_log) AS last_log', $sql);
        $this->assertStringContainsString('FROM logs', $sql);
        $this->assertStringContainsString('WHERE user_id =', $sql);
        $this->assertStringContainsString('GROUP BY indirizzo, device', $sql);
        $this->assertStringContainsString('ORDER BY last_log DESC', $sql);
        $this->assertStringContainsString('LIMIT 10', $sql);
        $this->assertStringContainsString('OFFSET 0', $sql);
    }

    public function testResetClearsSelectRaw(): void
    {
        $this->builder->selectRaw('COUNT(*) AS total');
        $this->builder->reset();
        $this->builder->from('logs');

        $sql = $this->builder->toSql();
        $this->assertStringContainsString('SELECT *', $sql);
        $this->assertStringNotContainsString('COUNT', $sql);
    }
}
