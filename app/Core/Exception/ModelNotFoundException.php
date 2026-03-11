<?php

declare(strict_types=1);

namespace App\Core\Exception;

class ModelNotFoundException extends \Exception {

    public function __construct( string $message,  int $code = 500) {
        parent::__construct($message, $code);
    }
}
