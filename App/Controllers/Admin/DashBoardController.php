<?php
namespace App\Controllers\Admin;

use App\Model\Contatti;
use App\Core\Facade\Auth;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Controllers\AdminController;
use App\Core\Controllers\AuthenticationController;

class DashBoardController extends AdminController{

    
    #[RouteAttr('/dashboard','GET', 'admin.dashboard')]
    public function index(){
       
        $messages = Contatti::orderBy('id', 'DESC')->get();
       
       return view('admin.dashboard', compact('messages'));
    }


   

}