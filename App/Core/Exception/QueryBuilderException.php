<?php
namespace App\Core\Exception;

use App\Core\Exception\Base\CoreException;
use Exception;
class QueryBuilderException extends Exception
{
    public function __construct(
        string $message = 'Exception in Query!',
        int $code = 500
    ) {
        // $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        // $caller = $trace[1] ?? null;

        // if ($caller && isset($caller['file'], $caller['line'])) {
        //     // Cambia il file e la linea dell’eccezione
        //     $this->file = $caller['file'];
        //     $this->line = $caller['line'];

        //     $message .= " (originated in {$caller['file']} on line {$caller['line']})";
        // }

        parent::__construct($message, $code);
    }
}
