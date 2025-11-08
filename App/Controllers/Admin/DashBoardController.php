<?php
namespace App\Controllers\Admin;

use App\Model\Contatti;
use App\Core\Controllers\AuthenticationController;
use App\Core\Facade\Auth;
use App\Core\Http\Attributes\RouteAttr;

class DashBoardController extends AuthenticationController{

    
    #[RouteAttr('/admin/dashboard','GET')]
    public function index(){
        $message = Contatti::orderBy('id', 'DESC')->get();
       return view('admin.dashboard', compact('message'));
    }

    public function logout(){
        $this->setLayout('default');
        Auth::logout();
        return $this->mvc->response->redirect('/login');
    }

}