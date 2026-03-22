<?php

declare(strict_types=1);

namespace App\Core\Controllers;

use App\Core\Http\Attributes\Middleware;

/**
 * Default base class for web controllers.
 *
 * It applies the guest middleware by default and is intended for
 * regular page-oriented controllers in userland applications.
 */
#[Middleware('guest')]
abstract class Controller extends BaseController {
    
}
