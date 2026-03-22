<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Model\Contatti;
use App\Core\Facade\Auth;
use App\Core\Controllers\AdminController;
use App\Core\Controllers\AuthenticationController;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Prefix;
use App\Services\VisitorService;

#[Prefix('/admin')]
#[Middleware('auth')]
class DashBoardController extends AdminController{


    #[Get('/dashboard', 'admin.dashboard')]
    public function index(){

        $messages = Contatti::query()->orderBy('id', 'DESC')->get();
        $totalVisits = VisitorService::getTotalVisits();
        $uniqueVisitors = VisitorService::getUniqueVisitors();
        $todayVisits = VisitorService::getTodayVisits();
        $dailyVisits = VisitorService::getVisitsByDay(7);

       return view('admin.dashboard', compact('messages', 'totalVisits', 'uniqueVisitors', 'todayVisits', 'dailyVisits'));
    }


   

}
