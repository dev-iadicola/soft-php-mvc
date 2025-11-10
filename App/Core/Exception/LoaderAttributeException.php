<?php

namespace App\Core\Exception;

use App\Core\Exception\Base\CoreException;
use Exception;

class LoaderAttributeException extends CoreException
{
    public function __construct(
        string $message = 'Exception in your Controller!',
        int $code = 500
    ) {
        parent::__construct($message, $code, 2);
    }
}
