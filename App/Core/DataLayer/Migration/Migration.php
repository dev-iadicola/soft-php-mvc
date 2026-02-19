<?php

namespace App\Core\DataLayer\Migration;

use App\Core\Config;
use App\Core\Database;
use PDO;
use PDOException;

class Migration
{
    private string $tableName;
    private array $columns = [];
    private array $constraints = [];
    private ?string $dropTable = null;
    private ?string $lastColumn = null;

    private function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public static function table(string $name): self
    {
        return new self($name);
    }

    // ──────────────────────────────────────
    //  Column types
    // ──────────────────────────────────────

    public function id(string $name = 'id'): self
    {
        $this->columns[$name] = "`$name` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
        $this->lastColumn = $name;
        return $this;
    }

    public function string(string $name, int $length = 255): self
    {
        $this->columns[$name] = "`$name` VARCHAR($length)";
        $this->lastColumn = $name;
        return $this;
    }

    public function text(string $name): self
    {
        $this->columns[$name] = "`$name` TEXT";
        $this->lastColumn = $name;
        return $this;
    }

    public function mediumText(string $name): self
    {
        $this->columns[$name] = "`$name` MEDIUMTEXT";
        $this->lastColumn = $name;
        return $this;
    }

    public function longText(string $name): self
    {
        $this->columns[$name] = "`$name` LONGTEXT";
        $this->lastColumn = $name;
        return $this;
    }

    public function year(string $name): self
    {
        $this->columns[$name] = "`$name` YEAR";
        $this->lastColumn = $name;
        return $this;
    }

    public function stringId(string $name, int $length = 255): self
    {
        $this->columns[$name] = "`$name` VARCHAR($length) NOT NULL PRIMARY KEY";
        $this->lastColumn = $name;
        return $this;
    }

    public function integer(string $name): self
    {
        $this->columns[$name] = "`$name` INT";
        $this->lastColumn = $name;
        return $this;
    }

    public function bigInteger(string $name): self
    {
        $this->columns[$name] = "`$name` BIGINT";
        $this->lastColumn = $name;
        return $this;
    }

    public function bool(string $name): self
    {
        $this->columns[$name] = "`$name` TINYINT(1)";
        $this->lastColumn = $name;
        return $this;
    }

    public function json(string $name): self
    {
        $this->columns[$name] = "`$name` JSON";
        $this->lastColumn = $name;
        return $this;
    }

    public function datetime(string $name): self
    {
        $this->columns[$name] = "`$name` DATETIME";
        $this->lastColumn = $name;
        return $this;
    }

    public function date(string $name): self
    {
        $this->columns[$name] = "`$name` DATE";
        $this->lastColumn = $name;
        return $this;
    }

    public function timestamp(string $name): self
    {
        $this->columns[$name] = "`$name` TIMESTAMP";
        $this->lastColumn = $name;
        return $this;
    }

    public function timestamps(): self
    {
        $this->columns['created_at'] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns['updated_at'] = "`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        $this->lastColumn = 'updated_at';
        return $this;
    }

    // ──────────────────────────────────────
    //  Column modifiers (apply to last column)
    // ──────────────────────────────────────

    public function nullable(): self
    {
        $this->appendToLast('NULL');
        return $this;
    }

    public function notNull(): self
    {
        $this->appendToLast('NOT NULL');
        return $this;
    }

    public function unsigned(): self
    {
        $this->appendToLast('UNSIGNED');
        return $this;
    }

    public function unique(): self
    {
        $this->appendToLast('UNIQUE');
        return $this;
    }

    public function default(mixed $value): self
    {
        if (is_null($value)) {
            $this->appendToLast('DEFAULT NULL');
        } elseif (is_bool($value)) {
            $this->appendToLast('DEFAULT ' . ($value ? '1' : '0'));
        } elseif (is_string($value)) {
            $escaped = addslashes($value);
            $this->appendToLast("DEFAULT '$escaped'");
        } else {
            $this->appendToLast("DEFAULT $value");
        }
        return $this;
    }

    public function defaultRaw(string $expression): self
    {
        $this->appendToLast("DEFAULT $expression");
        return $this;
    }

    // ──────────────────────────────────────
    //  Constraints
    // ──────────────────────────────────────

    public function foreignKey(string $column, string $refTable, string $refCol = 'id', string $onDelete = '', string $onUpdate = ''): self
    {
        $sql = "FOREIGN KEY (`$column`) REFERENCES `$refTable`(`$refCol`)";
        if ($onDelete) {
            $sql .= " ON DELETE $onDelete";
        }
        if ($onUpdate) {
            $sql .= " ON UPDATE $onUpdate";
        }
        $this->constraints[] = $sql;
        return $this;
    }

    public function uniqueComposite(array $columns): self
    {
        $cols = implode('`, `', $columns);
        $this->constraints[] = "UNIQUE KEY (`$cols`)";
        return $this;
    }

    public function primaryComposite(array $columns): self
    {
        $cols = implode('`, `', $columns);
        $this->constraints[] = "PRIMARY KEY (`$cols`)";
        return $this;
    }

    public function raw(string $sql): self
    {
        $this->columns[] = $sql;
        $this->lastColumn = null;
        return $this;
    }

    // ──────────────────────────────────────
    //  Rollback definition
    // ──────────────────────────────────────

    public function onDrop(string $tableName): self
    {
        $this->dropTable = $tableName;
        return $this;
    }

    // ──────────────────────────────────────
    //  SQL generation
    // ──────────────────────────────────────

    public function toCreateSql(): string
    {
        $parts = array_values($this->columns);
        $parts = array_merge($parts, $this->constraints);

        return "CREATE TABLE IF NOT EXISTS `{$this->tableName}` (\n    "
            . implode(",\n    ", $parts)
            . "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    }

    public function toDropSql(): string
    {
        $table = $this->dropTable ?? $this->tableName;
        return "DROP TABLE IF EXISTS `$table`;";
    }

    // ──────────────────────────────────────
    //  Execution (called by Migrator)
    // ──────────────────────────────────────

    public function executeUp(): void
    {
        $pdo = $this->getPdo();
        $sql = $this->toCreateSql();
        $pdo->exec($sql);
    }

    public function executeDown(): void
    {
        if (!$this->dropTable) {
            return;
        }
        $pdo = $this->getPdo();
        $sql = $this->toDropSql();
        $pdo->exec($sql);
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    // ──────────────────────────────────────
    //  Internal helpers
    // ──────────────────────────────────────

    private function appendToLast(string $modifier): void
    {
        if ($this->lastColumn && isset($this->columns[$this->lastColumn])) {
            $this->columns[$this->lastColumn] .= ' ' . $modifier;
        }
    }

    private function getPdo(): PDO
    {
        return Database::getInstance()->getConnection();
    }
}
