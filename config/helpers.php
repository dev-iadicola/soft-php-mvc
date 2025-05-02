<?php

use App\Core\Mvc;
use App\Core\Support\Collection\BuildAppFile;
use App\Core\Support\Collection\Collection;
use App\Core\Support\Debug\VarDumper;

// File: src/helpers.php
// Defines a global helper function available everywhere
if (!function_exists('setMvc')) {
    function setMvc(BuildAppFile $config) {
        $mvc = new Mvc($config);
        $GLOBALS['mvc'] = $mvc;
        $mvc->run();
    }
}

if (!function_exists('mvc')) {
    function mvc() {
        return $GLOBALS['mvc'] ?? null;
    }
}

if (! function_exists('dd')) {
    /**
     * Dump and Die: stampa variabili e termina l'esecuzione
     *
     * @param  mixed  ...$vars
     * @return void
     */
    function dd($vars): void
    {
        getSource();
        VarDumper::dd($vars);
    }

    if (! function_exists('getSource')) {
        function getSource()
        {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            echo '<div style="
            background: #2d2d2d;
            color: #f8f8f2;
            font-family: Consolas, Monaco, monospace;
            font-size: 13px;
            margin: 20px;
            padding: 15px;
            border-left: 4px solid #ff79c6;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(255, 121, 198, 0.3);
        ">';

            echo '<div style="margin-bottom: 10px; font-weight: bold; color: #ff79c6;">Call Stack (debug_backtrace):</div>';

            foreach ($trace as $i => $step) {
                $file = $step['file'] ?? '[internal]';
                $line = $step['line'] ?? 'n/a';
                $function = $step['function'] ?? 'unknown';

                echo '<div style="margin-bottom: 6px;">';
                echo "<span style='color: #8be9fd;'>#{$i}</span> ";
                echo "<span style='color: #50fa7b;'>{$function}()</span> ";
                echo "<span style='color: #f1fa8c;'>in</span> ";
                echo "<span style='color: #bd93f9;'>{$file}</span>:";
                echo "<span style='color: #f1fa8c;'>{$line}</span>";
                echo '</div>';
            }

            echo '</div>';
        }
    }
    if(! function_exists('dump')){
        function dump($vars)
        {
            getSource();
            VarDumper::dump($vars);
        }
    }
    
}
