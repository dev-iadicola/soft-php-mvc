<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Http\Attributes\AttributeRoute;
use App\Core\Mvc;

class ErrorsController extends Controller{
    public function __construct(public Mvc $mvc) {
        parent::__construct($mvc);
       
        $this->setLayout('coming-soon.php');
     
    }
    #[AttributeRoute('coming-soon')]
    public function repair() {
        if(getenv('MAINTENANCE') == 'true' || getenv('CLOUD') == 'true'){
            $this->render('coming-soon',[]);
            
        }else{
            $this->mvc->response->redirect('/');
        }
        
    }
}