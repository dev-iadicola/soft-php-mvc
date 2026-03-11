<?php

use PHPUnit\Framework\TestCase;
use App\Core\DataLayer\Query\MySqlBuilder;

class QBTest extends TestCase
{
    private MySqlBuilder $qb;

    protected function setUp(): void
    {
        // QueryBuilder “vuoto”, nessuna connessione
        $this->qb = new MySqlBuilder();
        $this->qb->from('users');
        $this->qb->setAllowedColumns(['id', 'name', 'active', 'p.created_at']);
    }

    public function testSimpleSelect()
    {
        $sql = $this->qb->select(['id', 'name'])->toSql();

        $this->assertStringContainsString('SELECT id, name FROM users', $sql);
        $this->assertStringEndsWith(';', $sql);
    }

    public function testWhereClause()
    {
        $sql = $this->qb
            ->select('*')
            ->where('active', '=', 1)
            ->toRawSql();

        $this->assertStringContainsString('WHERE active =', $sql);
    }

    public function testJoinClause()
    {
        $sql = $this->qb
            ->select('users.name, posts.title')
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->toRawSql();
        $this->assertStringContainsString('INNER JOIN posts ON users.id = posts.user_id', $sql);
    }

    public function testLeftJoinClause()
    {
        $sql = $this->qb
            ->select('*')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->toRawSql();
        $this->assertStringContainsString('LEFT JOIN posts ON users.id = posts.user_id', $sql);
    }

    public function testGroupByAndOrderBy()
    {
        $sql = $this->qb
            ->select('active, COUNT(id) as total')
            ->groupBy('active')
            ->orderBy('total', 'DESC')
            ->toRawSql();
        $this->assertStringContainsString('GROUP BY active', $sql);
        $this->assertStringContainsString('ORDER BY total DESC', $sql);
    }

    public function testWhereBetween()
    {
        $sql = $this->qb
            ->select('*')
            ->whereBetween('age', 18, 30)
            ->toRawSql();
        $this->assertStringContainsString('WHERE age BETWEEN', $sql);
    }

    public function testWhereNotBetween()
    {
        $sql = $this->qb
            ->select('*')
            ->whereNotBetween('age', 18, 30)
            ->toRawSql();

        $this->assertStringContainsString('WHERE age NOT BETWEEN', $sql);
    }

    public function testWhereInAndWhereNotIn()
    {
        $sql = $this->qb
            ->select('*')
            ->whereIn('id', [1, 2, 3])
            ->whereNotIn('active', [0])
            ->toRawSql();

        $this->assertStringContainsString('WHERE id IN', $sql);
        $this->assertStringContainsString('AND active NOT IN', $sql);
    }

    public function testMultipleClauses()
    {
        $sql = $this->qb
            ->select(['u.id', 'u.name', 'p.title'])
            ->join('posts p', 'u.id', '=', 'p.user_id')
            ->where('u.active', '=', 1)
            ->orderBy('p.created_at', 'DESC')
            ->limit(10)
            ->toRawSql();
        $this->assertStringContainsString('SELECT u.id, u.name, p.title FROM users', $sql);
        $this->assertStringContainsString('INNER JOIN posts p ON u.id = p.user_id', $sql);
        $this->assertStringContainsString('WHERE u.active =', $sql);
        $this->assertStringContainsString('ORDER BY p.created_at DESC', $sql);
        $this->assertStringContainsString('LIMIT 10', $sql);
    }

    public function testRawSqlReplacesBindings()
    {
        $this->qb->select('*')->where('id', '=', '42')->where('name', '=', 'John');

        $raw = $this->qb->toRawSql();

        $this->assertStringContainsString("42", $raw);
        $this->assertStringContainsString("'John'", $raw);
    }
}
