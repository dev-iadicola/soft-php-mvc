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

        return inertia('Admin/Visitors', [
            'meta' => [
                'title' => 'Statistiche visitatori',
            ],
            'visitorsPage' => [
                'metrics' => [
                    'totalVisits' => $totalVisits,
                    'todayVisits' => $todayVisits,
                    'uniqueVisitors' => $uniqueVisitors,
                    'todayUnique' => $todayUnique,
                ],
                'dailyVisits' => array_map(
                    static fn(array $row): array => [
                        'date' => (string) $row['date'],
                        'count' => (int) $row['count'],
                    ],
                    $dailyVisits
                ),
                'weeklyVisits' => array_map(
                    static fn(array $row): array => [
                        'week' => (string) $row['week'],
                        'count' => (int) $row['count'],
                    ],
                    $weeklyVisits
                ),
                'monthlyVisits' => array_map(
                    static fn(array $row): array => [
                        'month' => (string) $row['month'],
                        'count' => (int) $row['count'],
                    ],
                    $monthlyVisits
                ),
                'topBrowsers' => array_map(
                    static fn(array $row): array => [
                        'browser' => (string) $row['browser'],
                        'count' => (int) $row['count'],
                    ],
                    $topBrowsers
                ),
                'topDevices' => array_map(
                    static fn(array $row): array => [
                        'device' => (string) $row['device'],
                        'count' => (int) $row['count'],
                    ],
                    $topDevices
                ),
                'topPages' => array_map(
                    static fn(array $row): array => [
                        'url' => (string) $row['url'],
                        'count' => (int) $row['count'],
                    ],
                    $topPages
                ),
                'recentVisits' => array_map(
                    static fn(object $visit): array => [
                        'id' => (int) ($visit->id ?? 0),
                        'ip' => (string) ($visit->ip_address ?? ''),
                        'url' => (string) ($visit->url ?? ''),
                        'userAgent' => (string) ($visit->user_agent ?? ''),
                        'createdAt' => (string) ($visit->created_at ?? ''),
                    ],
                    $recentVisits
                ),
            ],
        ]);
    }
}
