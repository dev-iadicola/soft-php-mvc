<?php 
namespace App\Core\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\ControllerAttr;

#[ControllerAttr(['guest','web'])]
abstract class BaseController extends Controller {
    
}