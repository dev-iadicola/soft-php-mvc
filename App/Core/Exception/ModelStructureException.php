<?php

namespace App\Core\Exception;

class ModelStructureException extends \Exception
{
    public function __construct($message = "", $code = 0)
    {
        //parent::__construct($message, $code);
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $trace[1] ?? null;

        if ($caller && isset($caller['file'], $caller['line'])) {
            // Cambia il file e la linea dell’eccezione
            $this->file = $caller['file'];
            $this->line = $caller['line'];

            $message .= " (originated in {$caller['file']} on line {$caller['line']})";
        }

        parent::__construct($message, $code);
    }
}
