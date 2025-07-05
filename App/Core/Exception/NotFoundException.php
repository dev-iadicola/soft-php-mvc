<?php
namespace App\Core\Exception;

use App\Core\CLI\System\Out;

class NotFoundException extends \Exception {

    public function __construct(
        string $message = 'Page Not Found!',
         int $code = 404
    ) {
        parent::__construct($message, $code);
    }

}