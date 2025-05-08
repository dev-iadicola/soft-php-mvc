<?php

use App\Core\Connection\SMTP;
use App\Core\Design\Stilize;
use App\Core\Mvc;
use App\Core\Support\Collection\BuildAppFile;
use App\Core\Support\Debug\VarDumper;

// File: src/helpers.php
// Defines a global helper function available everywhere


if (!function_exists('setMvc')) {
    /**
     * Summary of setMvc:
     * It allows you to initialize the MVC Pattern as well as make access to the instance globally.
     * @param App\Core\Support\Collection\BuildAppFile $config
     * @return void
     */
    function setMvc(BuildAppFile $config)
    {
        $mvc = new Mvc($config);
        $GLOBALS['mvc'] = $mvc;
        $mvc->run();
    }
}

if (!function_exists('mvc')) {
    /**
     * Summary of mvc
     * This function allows to access the MVC istance, which is important and necessary for many framework operations
     * @return Mvc
     */
    function mvc()
    {
        return $GLOBALS['mvc'];
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and Die: Print variables and end execution
     * @param  mixed  ...$vars
     * @return void
     */
    function dd(...$vars): void
    {
        getSource();
        VarDumper::dd($vars);
    }

    if (!function_exists('css')) {
        function css(){
           return mvc()->config->folder->css;
        }
    }


    if (!function_exists('getSource')) {
        /**
         * Summary of getSource
         * @return void
         */
        function getSource()
        {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            Stilize::get('debugbacktrace.css');
            echo '<div class="debug-trace">';
  

            $trace = array_reverse($trace);
            foreach ($trace as $i => $step) {
                $file = $step['file'] ?? '[internal]';
                $line = $step['line'] ?? 'n/a';
                $function = $step['function'] ?? 'unknown';
                
                    echo "<span class='green'>{$function}()</span> ";
                    echo "<span class='yellow'>in</span> ";
                    echo "<span class='indaco'>{$file}</span>:";
                    echo "<span class='yellow'>{$line}</span> <br><br>";
                  
                    
            }
           
        }
       
    }
    if (!function_exists('dump')) {
        function dump($vars)
        {
            getSource();
            VarDumper::dump($vars);
        }
    }
    if (!function_exists('baseRoot')) {
        function baseRoot(): string
        {
            return $_SERVER['DOCUMENT_ROOT'];
        }
    }
    if (!function_exists('convertDotToSlash')) {
        function convertDotToSlash(string $dir)
        {
            return str_replace('.', '/', $dir);
        }
    }

    if (!function_exists(function: 'smtp')) {
        function smtp(): SMTP
        {
            return new SMTP();
        }
    }


}
