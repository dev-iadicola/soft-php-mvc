<?php 
namespace App\Core\Eloquent\Schema\Validation; 

use App\Core\Migration;
use PDO;
use App\Core\Database;
use App\Core\CLI\System\Out;

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
        //Out::debug('Tabella -> '.$table).
         Out::debug("FETCHCOLUM = ".$smtp->fetchColumn(). "  TAB " .$table);

        return $smtp->fetchColumn() > 0;
    
    }

     public static function getExistColumns($table): array{
        return Migration::findOrFail($table)->json_sql;
        // $smts = self::$pdo->query("DESCRIBE `{$table}`");
        // $columns = [];
      
        // while($row = $smts->fetch(PDO::FETCH_ASSOC)){
        //     foreach($row as $key => $value){
        //         Out::info("". $key ." => ". $value);
        //     }
        //     //$columns[$row["field"]] = $row ;
        // }
        // print_r($columns);

        // return $columns;
    }
 }