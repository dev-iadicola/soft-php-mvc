<?php 
namespace App\Core\Controllers;

use App\Core\Http\Attributes\ControllerAttr;

#[ControllerAttr(['guest'])]
abstract class Controller extends BaseController {
    
}