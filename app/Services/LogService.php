<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\LogTrace;

class LogService
{
    /**
     * Create a new log trace entry.
     */
    public static function create(int $userId, string $ip, string $device): void
    {
        LogTrace::query()->create([
            'user_id'   => $userId,
            'indirizzo' => $ip,
            'last_log'  => date('Y-m-d H:i:s'),
            'device'    => $device,
        ]);
    }

    /**
     * Get login statistics grouped by IP address and device.
     *
     * @return array<int, mixed>
     */
    public static function getLoginStats(): array
    {
        $query = "SELECT indirizzo, device, COUNT(*) AS login_count, MAX(last_log) AS last_log
        FROM logs
        GROUP BY indirizzo, device;";

        /** @var array<int, mixed> */
        return LogTrace::query()->query($query);
    }
}
