<?php
namespace App\Controllers\Admin;
use App\Core\Mvc;
use App\Core\Eloquent\ORM;
use App\Model\Contatti;
use App\Core\Controller;
use App\Model\Curriculum;
use App\Core\Services\AuthService;

class DashBoardController extends Controller{

    public function __construct(public Mvc $mvc) {
        parent::__construct($mvc);
        
        $this->setLayout('admin');
        
    }

    public function index(){

        $curricula = Curriculum::findAll();
        $message = Contatti::orderBy('id desc')->get();
        $cvdownload = (new ORM($this->mvc->pdo))->query('SELECT sum(download) as downloads from curriculum limit 1');
       

        $this->render('admin.dashboard',[],compact('curricula','message','cvdownload'));
    }

    public function logout(){
        $this->setLayout('default');
        AuthService::logout();
        return $this->mvc->response->redirect('/login');
    }

}