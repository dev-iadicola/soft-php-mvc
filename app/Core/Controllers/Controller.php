<?php

declare(strict_types=1);

namespace App\Core\Controllers;

use App\Core\Http\Attributes\ControllerAttr;

#[ControllerAttr(['guest'])]
abstract class Controller extends BaseController {
    
}
