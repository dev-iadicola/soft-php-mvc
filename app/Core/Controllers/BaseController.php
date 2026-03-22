<?php

declare(strict_types=1);

namespace App\Core\Controllers;

use App\Core\Http\Attributes\Middleware;
use \App\Core\Mvc;

/**
 * Base class for all framework controllers.
 *
 * It exposes the current MVC runtime so subclasses can access
 * request/response/config services when they need framework internals.
 * Rendering and redirects should happen through the global helpers.
 */
#[Middleware('web')]
abstract class BaseController
{
    protected Mvc $mvc; 
    public function __construct( ?Mvc $mvc = null)
    {
       $this->mvc = $mvc ?? Mvc::$mvc;
    }
}
