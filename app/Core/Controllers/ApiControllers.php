<?php

declare(strict_types=1);

namespace App\Core\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Http\Attributes\ControllerAttr;

#[ControllerAttr(['api'])]
class ApiControllers extends BaseController{

}
