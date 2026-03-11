<?php

declare(strict_types=1);

namespace App\Core\Enum;

enum HttpActionType: string
{
   case POST = '@csrf';
   case DELETE = '@delete';
   case PATCH = '@patch';
   case PUT = '@put';
}
