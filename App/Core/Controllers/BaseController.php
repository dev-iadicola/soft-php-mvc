<?php 
namespace App\Core\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\AttributeMiddleware;

#[AttributeMiddleware('guest')]
abstract class BaseController extends Controller {
    
}