<?php

declare(strict_types=1);

use App\Core\DataLayer\Migration\Migration;
use App\Core\Database;
use PHPUnit\Framework\TestCase;

class MigrationTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $reflection = new ReflectionClass(Database::class);
        $database = $reflection->newInstanceWithoutConstructor();

        $pdoProperty = $reflection->getProperty('pdo');
        $pdoProperty->setAccessible(true);
        $pdoProperty->setValue($database, $this->pdo);

        $instanceProperty = $reflection->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(null, $database);
    }

    public function testRawSqlMigrationWithNoStatementsIsANoop(): void
    {
        $migration = Migration::rawSql([], []);

        $migration->executeUp();
        $migration->executeDown();

        $tables = $this->pdo
            ->query("SELECT name FROM sqlite_master WHERE type = 'table'")
            ->fetchAll(PDO::FETCH_COLUMN);

        $this->assertSame([], $tables);
    }

    public function testRawSqlFiltersEmptyStatementsBeforeExecution(): void
    {
        $migration = Migration::rawSql([
            '   ',
            'CREATE TABLE demo (id INTEGER PRIMARY KEY)',
            '',
        ]);

        $migration->executeUp();

        $tables = $this->pdo
            ->query("SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'demo'")
            ->fetchAll(PDO::FETCH_COLUMN);

        $this->assertSame(['demo'], $tables);
    }
}
