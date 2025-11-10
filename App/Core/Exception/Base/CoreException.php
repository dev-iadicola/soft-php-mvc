<?php 
namespace App\Core\Exception\Base;

use Exception;

/**
 * Summary of CoreException
 * An abstract class to catch exceptions and show trhe actual line of codie that
 * doesn't comply with the framework guidelines.
 */
abstract class CoreException extends Exception {
    public function __construct(
        string $message = "An initial framework error occurred.",
        int $code = 500,
        int $traceId = 1
        ){
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
            $caller = $trace[$traceId]; 
            if($caller && isset($caller["file"], $caller["line"])){
              $this->file = $caller["file"];   
              $this->line = $caller["line"];
              
              $message .= " (Origineted in {$this->file} on line  {$this->line}";

            }

            parent::__construct($message, $code);
    }
}