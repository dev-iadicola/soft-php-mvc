<?php

declare(strict_types=1);

namespace App\Core\Exception;

use App\Core\Exception\Base\CoreException;

class ModelStructureException extends CoreException
{
    public function __construct($message = "", $code = 500)
    {
        parent::__construct($message, $code);
    }
}
