<?php

declare(strict_types=1);

namespace App\Core\Contract;

use App\Core\Http\Request;
use App\Core\Mvc;

interface MiddlewareInterface{

    public function exec(Request $request): mixed;

}
