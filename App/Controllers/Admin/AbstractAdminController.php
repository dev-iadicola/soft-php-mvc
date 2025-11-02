<?php 
namespace App\Controllers\Admin;


use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\AttributeMiddleware;

#[AttributeMiddleware('auth')]
abstract class AbstractAdminController extends Controller {

     public function __construct() {
        
        $this->setLayout('admin');
        
    }
}