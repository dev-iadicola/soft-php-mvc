<?php

declare(strict_types=1);

namespace App\Core\Exception;

class FileNotFoundException extends \Exception {

    public function __construct(
        ?string $message = 'File Not found!',
        ?int $code = 404
    ) {
        parent::__construct($message, $code);
    }

}
