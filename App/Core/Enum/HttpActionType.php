<?php

namespace App\Core\Enum;

enum HttpActionType: string
{
   case POST = '@csrf';
   case DELETE = '@delete';
   case PATCH = '@patch';
   case PUT = '@put';
}
