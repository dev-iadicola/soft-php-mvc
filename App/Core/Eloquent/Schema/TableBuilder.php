<?php 
namespace App\Core\Eloquent\Schema;

use PDO;

class TableBuilder {
    public function __construct(protected PDO $pdo, protected string $table,protected array $columns = [], protected bool $isCreate = false )
    {
        
    }
    public function setCreate(): self {
        $this->isCreate = true;
        return $this;
    }

    public function id(string $name = 'id'): self {
        $this->columns[] = "`$name` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }


    public function string(string $name, int $length = 255): self {
        $this->columns[] = "`$name` VARCHAR($length)";
        return $this;
    }

    public function integer(string $name): self {
        $this->columns[] = "`$name` INT";
        return $this;
    }

    public function timestamps(): self {
        $this->columns[] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    public function unique(string $name): self {
        $this->columns[] = "UNIQUE (`$name`)";
        return $this;
    }

    public function nullable(): self {
        $last = array_key_last($this->columns);
        if ($last !== null) {
            $this->columns[$last]['nullable'] = true;
        }
        return $this;
    }
    
    public function default(string|int|null $value): self {
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
    
    protected function buildColumn(array $column): string {
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
    
    public function build(): void {
        if ($this->isCreate) {
            $sqlParts = [];
    
            foreach ($this->columns as $column) {
                if (is_array($column)) {
                    $sqlParts[] = $this->buildColumn($column);
                } else {
                    $sqlParts[] = $column; // per FOREIGN KEY, UNIQUE ecc.
                }
            }
    
            $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (\n" . implode(",\n", $sqlParts) . "\n)";
            $this->pdo->exec($sql);
        } else {
            $sqlParts = [];
        
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
                $this->pdo->exec($sql);
            }
        }
        
    }
    
    public function create(): void {
        $this->setCreate()->build();
    }

    public function delete(){
        $sql = "DROP TABLE IF EXISTS `{$this->table}`";
        $this->pdo->exec($sql);
    }
}