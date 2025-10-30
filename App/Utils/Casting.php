<?php 
namespace App\Utils;
class Casting {

    public static function formatArray(array $array){
        $newArray = [];
        foreach($array as $key => $val){
            if(ctype_digit($val)){
                $val = (int) $val;
            }elseif(is_numeric($val)){
                $val = (float) $val;
            }
            $newArray[$key] = $val;
        }
        return $newArray;
    }
  
}