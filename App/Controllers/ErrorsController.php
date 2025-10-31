<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Http\Attributes\AttributeRoute;
use App\Core\Mvc;

class ErrorsController {
   
    #[AttributeRoute('coming-soon')]
    public function repair() {
        if(getenv('MAINTENANCE') == 'true' || getenv('CLOUD') == 'true'){
           return view('coming-soon');
            
        }else{
          return  redirect('/');
        }
        
    }
}