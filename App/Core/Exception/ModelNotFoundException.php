<?php 
namespace App\Core\Exception;

class ModelNotFoundException extends \Exception {

    public function __construct(public string $message, public int $code = 500) {
        parent::__construct($message, $code);
    }
}