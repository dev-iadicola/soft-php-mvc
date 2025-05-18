<?php
namespace App\Core\CLI\System;

use BadMethodCallException;
class Out
{

    public static function __callStatic($method, $args)
    {
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

        if (!method_exists($instance, $method)) {
            throw new BadMethodCallException("Method '$method' does not exist."); //dice che l'errore è  qui
        } else {
            return $instance->$method(...$args);
        }
    }


    private function printLn($str)
    {
        $this->log($str);
    }


    private function print(string $str, $type = 'i')
    {
        $this->log($str, $type, false);
    }


    private function printError(string $str, ?int $codeError = 400)
    {

        $this->log("Error:" . $str, 'e', true);
        $this->log('code: ', $codeError);
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach ($trace as $i => $step) {
            $file = $step['file'] ?? '[internal]';
            $line = $step['line'] ?? 'n/a';
            $function = $step['function'] ?? 'unknown';
            if ($i >= 1) {
                $this->print("#{$i} ", 'e');
                $this->print("{$function}() ", 'e');
                $this->print("{$file} ", 'e');
                $this->log("{$line} ", 'e');
            }



        }

        exit(1);
    }

    private function printOK(string $message, ?int $code = 200)
    {
        $this->log('Success: ' . $message, 's');
        $this->log('code: '. $code .'','i');

    }

    private function printWarn(string $message, ?int $code = 110)
    {
        $this->log('Warning: ' . $message, 'w');
        $this->log('code : ' . $code, 'w');

    }
    private function foreach(array|object $vars){
        foreach ($vars as $key => $value) {
            if(is_array($value)|is_object($value)){
                $this->foreach($value);
            }else{
                $this->printLn( $key . ' => ' . $value );
            }

        }
        
    }
    private function debug($message)
    {
        $this->log('' . $message, 'i');
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3); // limitiamo la profondità

        foreach ($trace as $key => $frame) {
            $caller = $trace[$key];
            $file = $caller['file'] ?? '[internal]';
            $line = $caller['line'] ?? 'n/a';
            echo "\033[90m  ↳ Called from $file:$line\033[0m\n";
        }
    }

    private function log(string $str, string $type = 'i', bool $toBakc = true)
    {
        $colors = [
            'e' => 31, //error - red
            's' => 36, //success - blue
            'w' => 33, //warning - yellow
            'i' => 32,  //info - green
        ];
        $color = $colors[$type] ?? 0;

        $string = $toBakc ? "\033[" . $color . "m" . $str . "\033[0m\n" : "\033[" . $color . "m" . $str . "\033[0m";

        echo $string;
    }

    public function exe(array $command)
    {

        $string = "";
        foreach ($command as $key => $value) {
            if ($key >= 2) {
                $string .= $value . ' ';
            }
        }

        $this->printLn($string);
    }
}