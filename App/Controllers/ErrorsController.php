<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Controllers\BaseController;
use App\Core\Http\Attributes\AttributeRoute;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Mvc;
use App\Utils\Enviroment;

class ErrorsController extends BaseController {
   
    // #[RouteAttr('coming-soon')]
    // public function repair() {
    //     if(Enviroment::isMaintenance()){
    //        return view('coming-soon');
    //     }else{
    //       return  redirect('/');
    //     }
        
    // }
}