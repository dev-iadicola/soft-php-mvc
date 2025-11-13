<?php
// namespace App\Core\DataLayer\Schema;

// use App\Core\DataLayer\Schema\Validation\CheckSchema;
// use App\Core\Migration;
// use PDO;
// use PDOException;
// use App\Core\Helpers\Log;
// use App\Core\CLI\System\Out;
// use App\Core\DataLayer\Schema\Column;
// use ReturnTypeWillChange;

// class TableBuilder
// {
//     /**
//      * Questa classe è ancora in fase di sviluppo e testing
//      * 
//      */
//     public string $id;
//     protected Column $column;
//     public function __construct(protected PDO $pdo, public string $table, protected array $columns = [], protected bool $tableExist = false)
//     {
//         $this->tableExist = CheckSchema::tableExist($this->table);
//         Out::warning($this->tableExist ? "table $this->table exist:true" : "table $this->table exist:false");
//     }
//     public function setCreate(): self
//     {

//         $this->tableExist = CheckSchema::tableExist($this->table);
//         return $this;
//     }

//     public function id(string|int|float $name = 'id'): self
//     {

//         $this->id = $name;
//         $this->columns[$this->id] = "`$name` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
//         return $this;
//     }

//     public function notAutoIncrement(): static
//     {
//         $this->columns[$this->id] = str_replace('AUTO_INCREMENT', '', $this->columns[$this->id]);
//         return $this;
//     }

//     public function stringId(string $name, int $length = 255): self
//     {
//         $this->id = $name;
//         $this->columns[$name] = "`$name` VARCHAR($length) PRIMARY KEY";
//         return $this;
//     }
//     public function string(string $name, int $length = 255): self
//     {
//         $this->columns[$name] = "`$name` VARCHAR($length)";
//         return $this;
//     }
//     public function integer(string $name): self
//     {
//         $this->columns[$name] = "`$name` INT";
//         return $this;
//     }

//     public function timestamps(): self
//     {
//         $this->columns['created_at'] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
//         $this->columns['updated_at'] = "`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ";
//         return $this;
//     }

//     public function json(string $name): self
//     {
//         $this->columns[$name] = "`$name` JSON";
//         return $this;
//     }

//     public function unique(string|array $name = ''): self
//     {
//         if ($name === '') {
//             $this->last('UNIQUE');
//         }else{
//             $columns = is_array($name) ? implode('`,`', $name) : $name;
//             $this->columns[] = "UNIQUE (`$columns`)";
//         }
//         return $this;
//     }

//     public function text($name)
//     {
//         $this->columns[$name] = "`$name` TEXT";
//     }

//     private function last(string $string): void
//     {
//         $last = array_key_last($this->columns);
//         if ($last !== null) {
//             $this->columns[$last] = $this->columns[$last] . ' ' . $string;
//         }
//     }

//     public function nullable(): self
//     {
//         $this->last('NULL');
//         return $this;
//     }
//     public function notNull(): self
//     {
//         $this->last('NOT NULL');
//         return $this;
//     }
//     public function unsigned(): self
//     {
//         $this->last('UNSIGNED');
//         return $this;
//     }

//     public function default(string|int|float|null $value): self
//     {
//         $val = is_string($value) ? "'$value'" : $value;
//         $this->last("DEFAULT $val");
//         return $this;
//     }



//     // Foreign Key
//     public function foreignKey(string $foreignKey, string $tableReference, string $columnReference = 'id'): self
//     {
//         $this->columns[] = "FOREIGN KEY (`{$foreignKey}`) REFERENCES `{$tableReference}`(`{$columnReference}`)";
//         return $this;
//     }

//     public function bool(string $name): self
//     {
//         $this->columns[$name] = "`$name` TINYINT";
//         return $this;
//     }

//     private function createTable()
//     {
//         Migration::setMigration($this->table, $this->columns);

//         $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (\n" . implode(",\n", array: $this->columns) . "\n) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;";
//         Out::info($sql);
//         // put the data in migration
//         $this->pdo->exec($sql);
//     }

//     private function updateTable()
//     {
        // $sqlParts = [];



        // foreach ($this->columns as $column) {
        //     if (is_array($column)) {
        //         $action = $column['action'] ?? 'add';
        //         $columnDef = $column;

        //         switch (strtolower($action)) {
        //             case 'add':
        //                 $sqlParts[] = "ADD COLUMN {$columnDef}";
        //                 break;
        //             case 'modify':
        //                 $sqlParts[] = "MODIFY COLUMN {$columnDef}";
        //                 break;
        //             case 'drop':
        //                 $sqlParts[] = "DROP COLUMN `{$column['name']}`";
        //                 break;
        //             // puoi aggiungere altri casi
        //         }
        //     } else {
        //         $sqlParts[] = $column; // per ALTER generici
        //     }
        // }

        // if (!empty($sqlParts)) {
        //     $sql = "ALTER TABLE `{$this->table}`\n" . implode(",\n", $sqlParts);
        //     Out::warning($sql);
        //     $this->pdo->exec($sql);
        // }
//     }



//     // Date
//     public function datetime(string $name): self
//     {
//         $this->columns[$name] = "`$name` DATETIME ";
//         return $this;
//     }

//     public function date(string $name): self
//     {
//         $this->columns[$name] = "`$name` DATE";
//         return $this;
//     }


//     // esecuzione scrittura della tabella
//     private function exec(): void
//     {
//         try {
//             if ($this->tableExist) {
//                 $this->updateTable();
//                 Out::success("Table is create {$this->table}");
//             } else {
//                 $this->createTable();
//                 Out::success("Table is update {$this->table}");
//             }
//         } catch (PDOException $e) {
//             Out::warning($e->getMessage() . " at line " . $e->getLine() . " at file " . $e->getFile());
//         }

//     }

//     // comando pubblico per la creazione della tabella
//     public function build(): void
//     {
//         $this->exec();
//     }

//     // comando pubblico per l'eliminazione della tab 
//     public function delete()
//     {
//         $sql = "DROP TABLE IF EXISTS `{$this->table}`";
//         $this->pdo->exec($sql);
//     }
// }