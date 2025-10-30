<?php

use App\Core\Connection\SMTP;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Design\Stilize;
use App\Core\Http\Router;
use App\Core\Mvc;
use App\Core\Support\Collection\BuildAppFile;
use App\Core\Support\Debug\VarDumper;
use App\Core\CLI\System\Out;
use App\Core\Facade\Session;

// File: src/helpers.php
// Defines a global helper function available everywhere


if (!function_exists('inizializeMvc')) {
    /**
     * Summary of setMvc:
     * It allows you to initialize the MVC Pattern as well as make access to the instance globally.
     * @deprecated non verrà più utilizzato, si inizializza in modo OOP non con metodi globali.
     * @param App\Core\Support\Collection\BuildAppFile $config
     * @return void
     */
    function inizializeMvc(BuildAppFile $config)
    {
        $mvc = new Mvc($config);
        $GLOBALS['mvc'] = $mvc;
        $mvc->run();
    }
}

if (!function_exists('setMvc')) {
    function setMvc(Mvc $mvc)
    {
        $GLOBALS['mvc'] = $mvc;
    }
}





if (!function_exists(function: 'redirect')) {
    function redirect($var)
    {
        mvc()->response->redirect($var);
    }
}
if (!function_exists(function: 'printLn')) {
    function printLn(string $var)
    {
        passthru("php soft print $var");
    }
}
if (!function_exists(function: 'view')) {
    function view(string $page, array $variables = [], array|null $message = null)
    {
        return mvc()->controller->render($page, $variables, $message);
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
        return $GLOBALS['mvc'] ?? null;
    }
}
if (!function_exists('urlExist')) {
    /**
     * Check if a URL exists by fetching its headers
     * @param string $url
     * @return bool
     */
    function urlExist($url)
    {
        if (empty($url)) return false;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true); // non scaricare il corpo
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2); // timeout breve (2 secondi)
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // segue redirect
        curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 400;
    }
}

if (!function_exists('debug')){
    /**
     * Debug dump ad dies for extreme deep debug without complication 
     */
    function debug(...$vars): void{
        VarDumper::debug( ...$vars );
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

    if (!function_exists(function: 'assets')) {
        function assets(string $file): string
        {
            return '/assets/' . $file;
        }
    }

    if (!function_exists('validateImagePath')) {
        function validateImagePath(string $path, string $fallback)
        {
            if (file_exists(mvc()->config->folder->root . $path))
                return $path;
            else
                return $fallback;
        }
    }




    if (!function_exists(function: 'css')) { //get css folder in assets folder
        /**
         * Summary of css
         * return the css path
         */
        function css()
        {
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

    if (!function_exists('logger')) {
        function lg(...$vars)
        { 
            getSource();
            VarDumper::lg(...$vars);
        }
    }
    if (!function_exists('dump')) {
        function dump(...$vars)
        { 
            getSource();
            VarDumper::dump(...$vars);
        }
    }
    if (!function_exists('baseRoot')) {
        /**
         * Summary of baseRoot
         * 
         * @return string rotirna semrpe la rotta di documento.
         */
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

    /**
     * Funzioni per la gestione delle flash session (messaggi di UI)
     */

    if (!function_exists(function: 'flashMessage')) {
        function flashMessage(string $key)
        {
            return Session::getFlash($key);
        }
    }
}
