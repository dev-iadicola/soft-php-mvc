<?php

declare(strict_types=1);

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
    private ?int $lastConstraintIndex = null;
    private ?array $pendingForeign = null;
    private array $rawUpSql = [];
    private array $rawDownSql = [];

    private function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public static function table(string $name): self
    {
        return new self($name);
    }

    /**
     * Create a raw SQL migration (useful for ALTER TABLE, data fixes, etc.)
     *
     * @param string|array $upSql
     * @param string|array|null $downSql
     */
    public static function rawSql(string|array $upSql, string|array|null $downSql = null): self
    {
        $self = new self('__raw__');
        $self->rawUpSql = self::normalizeRawSql($upSql);
        $self->rawDownSql = $downSql === null ? [] : self::normalizeRawSql($downSql);
        return $self;
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
    //  Constraints — Foreign Keys
    // ──────────────────────────────────────

    /**
     * Shorthand: ->foreignKey('col', 'ref_table', 'ref_col', 'CASCADE', 'CASCADE')
     */
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
        $this->lastConstraintIndex = array_key_last($this->constraints);
        return $this;
    }

    /**
     * Fluent FK: ->foreign('user_id')->references('users', 'id')->onDelete('CASCADE')
     */
    public function foreign(string $column): self
    {
        $this->flushPendingForeign();
        $this->pendingForeign = ['column' => $column];
        return $this;
    }

    public function references(string $table, string $column = 'id'): self
    {
        if ($this->pendingForeign) {
            $this->pendingForeign['refTable'] = $table;
            $this->pendingForeign['refCol'] = $column;
            $this->flushPendingForeign();
        }
        return $this;
    }

    public function onDelete(string $action): self
    {
        if ($this->lastConstraintIndex !== null && isset($this->constraints[$this->lastConstraintIndex])) {
            $this->constraints[$this->lastConstraintIndex] .= " ON DELETE $action";
        }
        return $this;
    }

    public function onUpdate(string $action): self
    {
        if ($this->lastConstraintIndex !== null && isset($this->constraints[$this->lastConstraintIndex])) {
            $this->constraints[$this->lastConstraintIndex] .= " ON UPDATE $action";
        }
        return $this;
    }

    // ──────────────────────────────────────
    //  Constraints — Indexes
    // ──────────────────────────────────────

    /**
     * ->index('email')
     * ->index(['first_name', 'last_name'])
     * ->index('email', 'idx_users_email')
     */
    public function index(string|array $columns, ?string $name = null): self
    {
        $cols = is_array($columns) ? $columns : [$columns];
        $colsSql = '`' . implode('`, `', $cols) . '`';
        $nameSql = $name ? " `$name`" : '';
        $this->constraints[] = "INDEX{$nameSql} ($colsSql)";
        $this->lastConstraintIndex = array_key_last($this->constraints);
        return $this;
    }

    /**
     * ->uniqueIndex('email')
     * ->uniqueIndex(['ip', 'user_agent'], 'unique_ip_ua')
     */
    public function uniqueIndex(string|array $columns, ?string $name = null): self
    {
        $cols = is_array($columns) ? $columns : [$columns];
        $colsSql = '`' . implode('`, `', $cols) . '`';
        $nameSql = $name ? " `$name`" : '';
        $this->constraints[] = "UNIQUE INDEX{$nameSql} ($colsSql)";
        $this->lastConstraintIndex = array_key_last($this->constraints);
        return $this;
    }

    /**
     * ->fullText('overview')
     * ->fullText(['title', 'overview'], 'ft_articles')
     */
    public function fullText(string|array $columns, ?string $name = null): self
    {
        $cols = is_array($columns) ? $columns : [$columns];
        $colsSql = '`' . implode('`, `', $cols) . '`';
        $nameSql = $name ? " `$name`" : '';
        $this->constraints[] = "FULLTEXT INDEX{$nameSql} ($colsSql)";
        $this->lastConstraintIndex = array_key_last($this->constraints);
        return $this;
    }

    // ──────────────────────────────────────
    //  Constraints — Composite keys
    // ──────────────────────────────────────

    public function uniqueComposite(array $columns): self
    {
        $cols = implode('`, `', $columns);
        $this->constraints[] = "UNIQUE KEY (`$cols`)";
        $this->lastConstraintIndex = array_key_last($this->constraints);
        return $this;
    }

    public function primaryComposite(array $columns): self
    {
        $cols = implode('`, `', $columns);
        $this->constraints[] = "PRIMARY KEY (`$cols`)";
        $this->lastConstraintIndex = array_key_last($this->constraints);
        return $this;
    }

    // ──────────────────────────────────────
    //  Raw SQL
    // ──────────────────────────────────────

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
        $this->flushPendingForeign();
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
        if (!empty($this->rawUpSql)) {
            foreach ($this->rawUpSql as $sql) {
                $pdo->exec($sql);
            }
            return;
        }
        if ($this->tableName === '__raw__') {
            return;
        }
        $sql = $this->toCreateSql();
        $pdo->exec($sql);
    }

    public function executeDown(): void
    {
        $pdo = $this->getPdo();
        if (!empty($this->rawDownSql)) {
            foreach ($this->rawDownSql as $sql) {
                $pdo->exec($sql);
            }
            return;
        }
        if ($this->tableName === '__raw__') {
            return;
        }
        if (!$this->dropTable) {
            return;
        }
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

    private function flushPendingForeign(): void
    {
        if ($this->pendingForeign && isset($this->pendingForeign['refTable'])) {
            $col = $this->pendingForeign['column'];
            $refTable = $this->pendingForeign['refTable'];
            $refCol = $this->pendingForeign['refCol'] ?? 'id';
            $sql = "FOREIGN KEY (`$col`) REFERENCES `$refTable`(`$refCol`)";
            $this->constraints[] = $sql;
            $this->lastConstraintIndex = array_key_last($this->constraints);
            $this->pendingForeign = null;
        }
    }

    /**
     * @param string|array $sql
     * @return array<int, string>
     */
    private static function normalizeRawSql(string|array $sql): array
    {
        $statements = is_array($sql) ? $sql : [$sql];

        return array_values(array_filter(
            array_map(static fn (mixed $statement): string => trim((string) $statement), $statements),
            static fn (string $statement): bool => $statement !== ''
        ));
    }

    private function getPdo(): PDO
    {
        return Database::getInstance()->getConnection();
    }
}
