<?php
namespace App\Core\Exception;

use App\Core\CLI\System\Out;

class QueryBuilderException extends \Exception {

    public function __construct(
        public string $message = 'Exception in Query!',
        public int $code = 404
    ) {
        parent::__construct($message, $code);
    }

}