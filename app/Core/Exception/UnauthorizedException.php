<?php

declare(strict_types=1);

namespace App\Core\Exception;

use RuntimeException;

class UnauthorizedException extends RuntimeException
{
    public function __construct(string $message = 'Unauthorized', int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
