<?php 
namespace App\Core\Eloquent\Schema;

use PDO;
use App\Core\Database; 

class SchemaBuilder extends Database {
 
    public PDO $pdo;

    public function __construct()
    {
        parent::__construct();
    }

    public function table(string $name)
    {
     return new TableBuilder($this->pdo, $name);
    }

    public function build(string $name): TableBuilder{
        return $this->table($name)->setCreate();
    }
    public function drop(string $name){
        $this->pdo->exec("DROP TABLE IF EXISTS `$name`");

    }
}