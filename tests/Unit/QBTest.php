<?php

use App\Core\CLI\System\Out;
use PHPUnit\Framework\TestCase;
use App\Core\Eloquent\QueryBuilder;

class QBTest extends TestCase
{
    private QueryBuilder $qb;

    protected function setUp(): void
    {
        // QueryBuilder “vuoto”, nessuna connessione
        $this->qb = new QueryBuilder();
        $pdoMock = $this->createMock(PDO::class);
        $this->qb->setPDO($pdoMock);
        $this->qb->setTable('users');
        $this->qb->setFillable(['id', 'name', 'active', 'p.created_at']);
    }

    public function testSimpleSelect()
    {
        $sql = $this->qb->select(['id', 'name'])->toSql();

        Out::info("Query testSimpleSelect => " . $sql);
        $this->assertStringContainsString('SELECT id, name FROM users', $sql);
        $this->assertStringEndsWith(';', $sql);
    }

    public function testWhereClause()
    {
        $sql = $this->qb
            ->select('*')
            ->where('active', '=', 1)
            ->toRawSql();

        Out::info("Query testWhereClause => " . $sql);

        $this->assertStringContainsString('WHERE active =', $sql);
    }

    public function testJoinClause()
    {
        $sql = $this->qb
            ->select('users.name, posts.title')
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->toRawSql();
        Out::info("Query testJoinClause => " . $sql);
        $this->assertStringContainsString('INNER JOIN posts ON users.id = posts.user_id', $sql);
    }

    public function testLeftJoinClause()
    {
        $sql = $this->qb
            ->select('*')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->toRawSql();
        Out::info("Query testLeftJoinClause=> " . $sql);

        $this->assertStringContainsString('LEFT JOIN posts ON users.id = posts.user_id', $sql);
    }

    public function testGroupByAndOrderBy()
    {
        $sql = $this->qb
            ->select('active, COUNT(id) as total')
            ->groupBy('active')
            ->orderBy('total', 'DESC')
            ->toRawSql();
        Out::warn("Query testGroupByAndOrderBy => " . $sql);
        $this->assertStringContainsString('GROUP BY active', $sql);
        $this->assertStringContainsString('ORDER BY total DESC', $sql);
    }

    public function testWhereBetween()
    {
        $sql = $this->qb
            ->select('*')
            ->whereBetween('age', 18, 30)
            ->toRawSql();
        Out::info("Query testWhereBetween=> " . $sql);
        $this->assertStringContainsString('WHERE age BETWEEN', $sql);
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
         Out::warn("Query testMultipleClauses => " . $sql);
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
