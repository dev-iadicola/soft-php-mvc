<?php
namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Core\Config;
use App\Core\Controller;
use App\Core\Http\Request;

class MaintenanceController extends Controller{

    public function __construct(public Mvc $mvc)
    {
      parent::__construct($mvc);
  
      $this->setLayout('admin');
    }
  
    public function index(){
        $env = getenv('MAINTENANCE');
        return $this->render('admin.settings',[],compact('env'));
    }

    public function submit(Request $request){
        $post = $request->getPost();


        $root = $this->mvc->config->folder->root.'\.env';
        if(isset($post['check'])){

            $valueForEnv = 'true';
            Config::updateEnv($root,'MAINTENANCE',$valueForEnv);
            $this->withSuccess('SITO IN STATO DI MANUTENZIONE');
        }else{

            $valueForEnv = 'FALSE';
            Config::updateEnv($root,'MAINTENANCE',$valueForEnv);
            $this->withSuccess('SITO ATTIVATO - MANUTENZIONE DISATTIVATA');

        }

       return $this->redirectBack();

    }
}