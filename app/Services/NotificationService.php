<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\Notification;

class NotificationService
{
    public static function create(string $type, string $title, ?string $message = null, ?string $link = null): Notification
    {
        Notification::query()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);

        /** @var Notification */
        return Notification::query()->orderBy('id', 'DESC')->first();
    }

    /**
     * @return array<int, Notification>
     */
    public static function getUnread(int $limit = 10): array
    {
        return Notification::query()
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    public static function countUnread(): int
    {
        return Notification::query()->where('is_read', 0)->count();
    }

    public static function markAsRead(int $id): void
    {
        Notification::query()->where('id', $id)->update(['is_read' => 1]);
    }

    public static function markAllAsRead(): void
    {
        Notification::query()->where('is_read', 0)->update(['is_read' => 1]);
    }

    public static function findOrFail(int $id): Notification
    {
        /** @var Notification|null $notification */
        $notification = Notification::query()->find($id);

        if ($notification === null) {
            throw new \App\Core\Exception\NotFoundException("Notification with id {$id} not found");
        }

        return $notification;
    }
}
