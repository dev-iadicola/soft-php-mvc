<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Post;
use App\Core\Http\Attributes\Prefix;
use App\Services\NotificationService;

#[Prefix('/admin')]
#[Middleware('auth')]
class NotificationController extends AdminController
{
    #[Get('/notifications/count', 'admin.notifications.count')]
    public function count()
    {
        try {
            $count = NotificationService::countUnread();
        } catch (\Throwable) {
            $count = 0;
        }

        return response()->json(['count' => $count]);
    }

    #[Post('/notifications/{id}/read', 'admin.notifications.read')]
    public function markAsRead(int $id)
    {
        $notification = NotificationService::findOrFail($id);
        NotificationService::markAsRead($id);

        if ($notification->link) {
            return redirect($notification->link);
        }

        return response()->back();
    }

    #[Post('/notifications/read-all', 'admin.notifications.readAll')]
    public function markAllAsRead()
    {
        NotificationService::markAllAsRead();
        return response()->back()->withSuccess('Tutte le notifiche segnate come lette.');
    }
}
