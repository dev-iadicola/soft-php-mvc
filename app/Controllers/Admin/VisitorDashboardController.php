<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Prefix;
use App\Services\VisitorService;

#[Prefix('/admin')]
#[Middleware('auth')]
class VisitorDashboardController extends AdminController
{
    #[Get('/visitors', 'admin.visitors')]
    public function index()
    {
        $totalVisits = VisitorService::getTotalVisits();
        $todayVisits = VisitorService::getTodayVisits();
        $uniqueVisitors = VisitorService::getUniqueVisitors();
        $todayUnique = VisitorService::getTodayUniqueVisitors();
        $dailyVisits = VisitorService::getVisitsByDay(30);
        $weeklyVisits = VisitorService::getVisitsByWeek(12);
        $monthlyVisits = VisitorService::getVisitsByMonth(12);
        $topBrowsers = VisitorService::getTopBrowsers(6);
        $topDevices = VisitorService::getTopDevices();
        $topPages = VisitorService::getTopPages(10);
        $recentVisits = VisitorService::getRecent(30);

        return view('admin.visitors', compact(
            'totalVisits',
            'todayVisits',
            'uniqueVisitors',
            'todayUnique',
            'dailyVisits',
            'weeklyVisits',
            'monthlyVisits',
            'topBrowsers',
            'topDevices',
            'topPages',
            'recentVisits'
        ));
    }
}
