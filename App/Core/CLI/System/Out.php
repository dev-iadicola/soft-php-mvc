<?php 
namespace App\Core\CLI\System;

use BadMethodCallException;
class Out {

    public static function __callStatic($method, $args){
        $instance = new self();
        
        $aliases = [
            'error' => 'printError',
            'warn' => 'printWarn',
            'warning' => 'printWarn',
            'ok' => 'printOK',
            'ln' => 'printLn',
            'p' => 'print',
        ];
    
        $method = $aliases[$method] ?? $method;

        if (!method_exists($instance, $method)){
            throw new BadMethodCallException("Method '$method' does not exist."); //dice che l'errore è  qui
        }else{
            return $instance->$method(...$args);
        }
    } 


    private function printLn($str) {
        $this->log($str);
        
    }
    private function print(string $str) {
        $this->log($str,'',false);
    }


     private function printError($str) {

        $this->log("Error:" . $str, 'e');
        exit(1);
    }

    private function printOK($message) {
        $this->log('Success: '. $message,'s');

    }

    private function printWarn($message) {
       $this->log('Warning: '. $message,'w');
      
    }

    private function log(string $str, string $type = 'i', bool $toBakc = true) {
        $colors = [
            'e' => 31, //error
            's' => 32, //success
            'w' => 33, //warning
            'i' => 36  //info
        ];
        $color = $colors[$type] ?? 0;
        
        $string = $toBakc ? "\033[".$color."m".$str."\033[0m\n" :"\033[".$color."m".$str."\033[0m";
        echo $string;
    
    }
}