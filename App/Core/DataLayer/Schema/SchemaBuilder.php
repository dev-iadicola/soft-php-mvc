<?php 
// namespace App\Core\DataLayer\Schema;

// use PDO;
// use App\Core\Database; 
// use App\Core\CLI\System\Out;
// use App\Core\DataLayer\Model;

// class SchemaBuilder  {
 
//     public TableBuilder $tableBuilder;
//     public function __construct()
//     {
//         parent::__construct();
        
//     }

//     public function table(string $table)
//     {
//        return new TableBuilder($this->pdo, $table);
//     }

//     // public function build(string $name): TableBuilder{
//     //     return $this->table($name)->setCreate();
//     // }
//     public function drop(string $name){
//         $this->pdo->exec("DROP TABLE IF EXISTS `$name`");

//     }
// }