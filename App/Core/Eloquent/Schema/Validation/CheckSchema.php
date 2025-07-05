<?php 
namespace App\Core\Eloquent\Schema\Validation; 

use App\Core\Migration;
use PDO;
use App\Core\Database;

 class CheckSchema extends Database {

    
    public function __construct(){
        parent::__construct();

    }
    public static function tableExist($table): bool{
        $sql = "SELECT COUNT(*) 
            FROM information_schema.tables 
            WHERE table_schema = DATABASE() 
            AND table_name = :table";
    
        $smtp = database()->pdo->prepare($sql);
        $smtp->execute(['table' => $table]);


        return $smtp->fetchColumn() > 0;
    
    }

     
 }