<?php
namespace App\Controllers\Admin;
use App\Core\Mvc;
use App\Core\Eloquent\Model;
use App\Model\Contatti;
use App\Core\Controller;
use App\Core\Facade\Auth;
use App\Core\Http\Attributes\AttributeRoute;
use App\Core\Services\AuthService;

class DashBoardController extends AbstractAdminController{

    
    #[AttributeRoute('/admin/dashboard','')]
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