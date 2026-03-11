<?php

declare(strict_types=1);

namespace App\Core\Exception;

use RuntimeException;

class ValidationException extends RuntimeException
{
    private array $errors;

    public function __construct(array $errors, string $message = 'Validation failed', int $code = 422)
    {
        $this->errors = $errors;
        parent::__construct($message, $code);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
