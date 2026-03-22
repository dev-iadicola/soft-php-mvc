<?php

declare(strict_types=1);

namespace App\Core\DataLayer\Seeder;

use App\Core\Database;
use PDO;

class Seeder
{
    private string $tableName;
    private array $rows = [];

    private function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public static function table(string $name): self
    {
        return new self($name);
    }

    public function row(array $data): self
    {
        $this->rows[] = $data;
        return $this;
    }

    // ──────────────────────────────────────
    //  Execution (called by SeederRunner)
    // ──────────────────────────────────────

    public function execute(): int
    {
        if (empty($this->rows)) {
            return 0;
        }

        $pdo = $this->getPdo();
        $columns = array_keys($this->rows[0]);
        $colsSql = '`' . implode('`, `', $columns) . '`';

        $placeholder = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
        $placeholders = implode(",\n    ", array_fill(0, count($this->rows), $placeholder));

        $sql = "INSERT INTO `{$this->tableName}` ($colsSql) VALUES\n    $placeholders;";

        $values = [];
        foreach ($this->rows as $row) {
            foreach ($columns as $col) {
                $values[] = $row[$col] ?? null;
            }
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);

        return count($this->rows);
    }

    public function rollback(): void
    {
        $pdo = $this->getPdo();
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->exec("TRUNCATE TABLE `{$this->tableName}`;");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    }

    // ──────────────────────────────────────
    //  Getters
    // ──────────────────────────────────────

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    // ──────────────────────────────────────
    //  Internal helpers
    // ──────────────────────────────────────

    private function getPdo(): PDO
    {
        return Database::getInstance()->getConnection();
    }
}
