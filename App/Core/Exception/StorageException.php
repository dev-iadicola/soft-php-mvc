<?php 
namespace App\Core\Exception;

class StorageException extends \Exception {

    public function __construct(string $message, int $code = 500) {
        parent::__construct($message, $code);
    }
}