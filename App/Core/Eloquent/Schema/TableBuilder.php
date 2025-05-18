<?php
namespace App\Core\Eloquent\Schema;

use App\Core\Eloquent\Schema\Validation\CheckSchema;
use App\Core\Migration;
use PDO;
use PDOException;
use App\Core\Helpers\Log;
use App\Core\CLI\System\Out;
use App\Core\Eloquent\Schema\Column;

class TableBuilder 
{
    public string $id;
    protected Column $column;
    public function __construct(protected PDO $pdo, protected string $table, protected array $columns = [], protected bool $tableExist = false)
    {

    }
    public function setCreate(): self
    {
        $this->tableExist = CheckSchema::tableExist($this->table);
        return $this;
    }

    public function id(string|int|float $name = 'id'): self
    {

        $this->id = $name;
        $this->columns[$this->id] = "`$name` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    public function notAutoIncrement(): static
    {
        $this->columns[$this->id] = str_replace('AUTO_INCREMENT', '', $this->columns[$this->id]);
        return $this;
    }

    public function stringId(string $name): self
    {
        $this->id = $name;
        $this->columns[$name] = "`$name` STRING PRIMARY KEY";
        return $this;
    }
    public function string(string $name, int $length = 255): self
    {
        $this->columns[$name] = "`$name` VARCHAR($length)";
        return $this;
    }
    public function integer(string $name): self
    {
        $this->columns[$name] = "`$name` INT";
        return $this;
    }

    public function timestamps(): self
    {
        $this->columns['created_at'] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns['updated_at'] = "`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    public function json(string $name){
        $this->columns[$name] = "`$name` JSON";
    }

    public function unique(string|array $name): self
    {
        $this->columns[] = "UNIQUE (`...$name`)";
        return $this;
    }
    public function text($name)
    {
        $this->columns[$name] = "`$name` TEXT";
    }

    public function nullable(): self
    {
        $last = array_key_last($this->columns);
        if ($last !== null) {
            $this->columns[$last]['nullable'] = true;
        }
        return $this;
    }

    public function default(string|int|null $value): self
    {
        $last = array_key_last($this->columns);
        if ($last !== null) {
            $this->columns[$last]['default'] = $value;
        }
        return $this;
    }


    public function foreignKey(string $foreignKey, string $columnReference, string $tableReference): self
    {
        $this->columns[] = "CONSTRAINT `FK_{$foreignKey}_{$columnReference}` FOREIGN KEY (`{$columnReference}`) REFERENCES `{$tableReference}`(`{$columnReference}`)";
        return $this;
    }

    protected function buildColumn(array $column): string
    {
        $sql = "`{$column['name']}` {$column['type']}";

        if ($column['nullable']) {
            $sql .= " NULL";
        } else {
            $sql .= " NOT NULL";
        }

        if (!is_null($column['default'])) {
            $default = is_string($column['default']) ? "'{$column['default']}'" : $column['default'];
            $sql .= " DEFAULT {$default}";
        }

        if (!empty($column['extra'])) {
            $sql .= " {$column['extra']}";
        }

        return $sql;
    }



    private function createTable()
    {
        Migration::setMigration($this->table, $this->columns);
        $table = $this->table; 
        $sqlParts = [];


        foreach ($this->columns as $column) {
            if (is_array($column)) {
                $sqlParts[] = $this->buildColumn($column);
            } else {
                $sqlParts[] = $column; // per FOREIGN KEY, UNIQUE ecc.
            }
        }

        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (\n" . implode(",\n", $sqlParts) . "\n)";
        Out::info($sql);
        $this->pdo->exec($sql);
    }

    private function updateTable()
    {
        $sqlParts = [];

        $existing = CheckSchema::getExistColumns($this->table);
        Log::info($existing);
        exit();

        foreach ($this->columns as $column) {
            if (is_array($column)) {
                $action = $column['action'] ?? 'add';
                $columnDef = $this->buildColumn($column);

                switch (strtolower($action)) {
                    case 'add':
                        $sqlParts[] = "ADD COLUMN {$columnDef}";
                        break;
                    case 'modify':
                        $sqlParts[] = "MODIFY COLUMN {$columnDef}";
                        break;
                    case 'drop':
                        $sqlParts[] = "DROP COLUMN `{$column['name']}`";
                        break;
                    // puoi aggiungere altri casi
                }
            } else {
                $sqlParts[] = $column; // per ALTER generici
            }
        }

        if (!empty($sqlParts)) {
            $sql = "ALTER TABLE `{$this->table}`\n" . implode(",\n", $sqlParts);
            //Out::info($sql);
            $this->pdo->exec($sql);
        }
    }
    public function build(): void
    {
        try {
            if ($this->tableExist) {
                $this->updateTable();
            } else {
                $this->createTable();
            }
        } catch (PDOException $e) {
            Out::info($e->getMessage(). " at line " . $e->getLine() . " at file " . $e->getFile());
            Out::error("Query fallita:\n" . ($sql ?? 'Query non disponibile.'));

        }

    }



    public function create(): void
    {
        $this->setCreate()->build();
    }

    public function delete()
    {
        $sql = "DROP TABLE IF EXISTS `{$this->table}`";
        $this->pdo->exec($sql);
    }
}