<?php

namespace App\Core\Provider;

use ErrorException;
use App\Core\Helpers\Log;

class NativeErrorProvider
{   

    /**
     * Summary of register
     *  Enable the programs's error and crash handlers and register all error and warning in app.log file
     * @return void
     */
    public function register()
    {
        // handle for "non-fatal" error. (eg. warning, notice, deprecated)
        set_error_handler([$this, 'waringPhpError']);
        // Handle fatal error to close script
        register_shutdown_function([$this,'fatalError']);
    }

    /**
     * Handles all PHP errors that do NOT srop the script. 
     *
     * @return void
     */
    public function waringPhpError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        // Crea un'eccezione a partire dai dati dell'errore
        $exception = new ErrorException($errstr, 0, $errno, $errfile, $errline);

        // Registra l'errore nel log
        Log::exception($exception);

        // Restituisce false per lasciare che PHP/Woops continui la gestione standard
        return false;
    }

    /**
     * Summary of fatalError
     * handle all Fatal Error to force close the script PHP
     * @return void
     */
    public function fatalError()
    {
        $error = error_get_last(); // get last PHP error.

        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE], true)) {
            $exception = new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
            // sing in the app.log
            Log::exception($exception);
        }
    }
}
