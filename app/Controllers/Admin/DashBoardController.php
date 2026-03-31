<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Prefix;
use App\Services\ArticleService;
use App\Services\ContactService;
use App\Services\ProjectService;
use App\Services\VisitorService;

#[Prefix('/admin')]
#[Middleware('auth')]
class DashBoardController extends AdminController
{
    #[Get('/dashboard', 'admin.dashboard')]
    public function index()
    {
        $messages = ContactService::getAll();
        $unreadCount = ContactService::countUnread();
        $totalProjects = count(ProjectService::getActive());
        $totalArticles = count(ArticleService::getActive());
        $totalVisits = VisitorService::getTotalVisits();
        $uniqueVisitors = VisitorService::getUniqueVisitors();
        $todayVisits = VisitorService::getTodayVisits();
        $dailyVisits = VisitorService::getVisitsByDay(7);

        return view('admin.dashboard', compact(
            'messages',
            'unreadCount',
            'totalProjects',
            'totalArticles',
            'totalVisits',
            'uniqueVisitors',
            'todayVisits',
            'dailyVisits',
        ));
    }


   

}
