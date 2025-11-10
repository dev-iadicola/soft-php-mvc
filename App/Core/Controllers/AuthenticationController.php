<?php 
namespace App\Core\Controllers;

use App\Core\Http\Attributes\ControllerAttr;

#[ControllerAttr(['auth'], '/admin')] 
abstract class AuthenticationController extends BaseController {
    
    protected string $defaultLayout = 'admin';
    public function __construct(\App\Core\Mvc|null $mvc = null){
        parent::__construct($mvc);     // eredita eventuali setup del parent
        $this->setLayout($this->defaultLayout);  // sovrascrive il layout
    }
    

}