<?php
namespace App\Core\Exception;

use App\Core\CLI\System\Out;

class NotFoundException extends \Exception {

    public function __construct(
        public string $message = 'Page Not Founf!',
        public int $code = 404
    ) {
        parent::__construct($message, $code);
    }

}