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
            'success' => 'printOK',
            'ln' => 'printLn',
            'info' => 'printLn',
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

 
    private function print(string $str, $type = 'i') {
        $this->log($str,$type,false);
    }


     private function printError($str) {

        $this->log("Error:" . $str, 'e',true);
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach($trace as $i => $step){
            $file = $step['file'] ?? '[internal]';
            $line = $step['line'] ?? 'n/a';
            $function = $step['function'] ?? 'unknown';
            if($i >= 1){
                $this->print("#{$i} ",'e');             
                $this->print("{$function}() ",'e');             
                $this->print("{$file} ",'e');             
                $this->log("{$line} ",'e');  
            }
                     

            
        }
       
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
            'e' => 31, //error - red
            's' => 36, //success - blue
            'w' => 33, //warning - yellow
            'i' => 32,  //info - green
        ];
        $color = $colors[$type] ?? 0;
        
        $string = $toBakc ? "\033[".$color."m".$str."\033[0m\n" :"\033[".$color."m".$str."\033[0m";
        echo $string;
    
    }
}