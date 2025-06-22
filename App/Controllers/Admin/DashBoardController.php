<?php
namespace App\Controllers\Admin;
use App\Core\Mvc;
use App\Core\Eloquent\Model;
use App\Model\Contatti;
use App\Core\Controller;
use App\Core\Services\AuthService;

class DashBoardController extends Controller{

    public function __construct(public Mvc $mvc) {
        parent::__construct($mvc);
        
        $this->setLayout('admin');
        
    }

    public function index(){
        $message = Contatti::orderBy('id desc')->get();
       return view('admin.dashboard', compact('message'));
    }

    public function logout(){
        $this->setLayout('default');
        AuthService::logout();
        return $this->mvc->response->redirect('/login');
    }

}