<?php 

use App\Core\Design\Stilize;
use App\Core\Support\Debug\VarDumper;
if (!function_exists('debug')) {
    /**
     * Debug dump ad dies for extreme deep debug without complication 
     */
    function debug(...$vars): void
    {
        VarDumper::debug(...$vars);
    }
}
/**
 * soft debug, not exit().
 */
if (!function_exists('sdb')) {
    function sdb(...$vars)
    {
        getSource();
        VarDumper::softdb(...$vars);
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
        VarDumper::dd(...$vars);
    }
}


if (!function_exists('dump')) {
    function dump(...$vars)
    {
        getSource();
        VarDumper::dump(...$vars);
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
