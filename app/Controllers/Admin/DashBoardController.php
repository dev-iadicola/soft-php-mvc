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

        return inertia('Admin/Dashboard', [
            'meta' => [
                'title' => 'Dashboard admin',
            ],
            'dashboard' => [
                'stats' => [
                    ['href' => '/admin/visitors', 'label' => 'Visite Totali', 'value' => number_format($totalVisits)],
                    ['href' => '/admin/contatti', 'label' => 'Messaggi non letti', 'value' => (string) $unreadCount],
                    ['href' => '/admin/visitors', 'label' => 'Visitatori Unici', 'value' => number_format($uniqueVisitors)],
                    ['href' => '/admin/project', 'label' => 'Progetti Attivi', 'value' => (string) $totalProjects],
                    ['href' => '/admin/home', 'label' => 'Articoli Attivi', 'value' => (string) $totalArticles],
                ],
                'todayVisits' => $todayVisits,
                'dailyVisits' => array_map(
                    static fn(array $row): array => [
                        'date' => (string) $row['date'],
                        'count' => (int) $row['count'],
                    ],
                    $dailyVisits
                ),
                'messages' => array_map(
                    static fn(object $message): array => [
                        'id' => (int) ($message->id ?? 0),
                        'name' => (string) ($message->nome ?? ''),
                        'typology' => (string) ($message->typologie ?? ''),
                        'excerpt' => mb_substr((string) ($message->messaggio ?? ''), 0, 120),
                        'createdAt' => (string) ($message->created_at ?? ''),
                        'isRead' => (bool) ($message->is_read ?? false),
                    ],
                    array_slice($messages, 0, 12)
                ),
            ],
        ]);
    }


   

}
