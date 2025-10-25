<?php 
namespace App\Core\Eloquent\Schema\Validation; 

use App\Core\Migration;
use PDO;
use App\Core\Database;

 class CheckSchema  {

    private ?Database $_database = null;
   

    private function database():Database{
        return Database::getInstance();
    }
    public static function tableExist($table): bool{
        $sql = "SELECT COUNT(*) 
            FROM information_schema.tables 
            WHERE table_schema = DATABASE() 
            AND table_name = :table";
    
        $smtp =  Database::getInstance()->getConnection()->prepare($sql); //->pdo->prepare($sql);
        $smtp->execute(['table' => $table]);


        return $smtp->fetchColumn() > 0;
    
    }

     
 } 