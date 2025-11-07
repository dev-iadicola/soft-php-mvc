<?php 
namespace App\Controllers\Admin;


use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\AttributeMiddleware;
use App\Core\Http\Attributes\ControllerAttr;

#[ControllerAttr(middlewareNames: 'auth',basePath: '/admin')]
abstract class AbstractAdminController extends Controller {

     public function __construct() {
        
        $this->setLayout('admin');
        
    }
}