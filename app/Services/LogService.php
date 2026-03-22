<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Helpers\Paginator;
use App\Core\Repository\LogRepository;
use App\Model\LogTrace;

class LogService
{
    private static ?LogRepository $repository = null;

    private static function repo(): LogRepository
    {
        if (self::$repository === null) {
            self::$repository = new LogRepository();
        }
        return self::$repository;
    }

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
     * @return array<int, array<string, mixed>>
     */
    public static function getLoginStats(): array
    {
        return LogTrace::query()
            ->select(['indirizzo', 'device'])
            ->selectAggregate('COUNT', '*', 'login_count')
            ->selectAggregate('MAX', 'last_log', 'last_log')
            ->groupBy(['indirizzo', 'device'])
            ->fetchRows();
    }

    /**
     * Get paginated and filtered login stats.
     *
     * @param array<string, mixed> $filters
     */
    public static function getPaginated(array $filters, int $page = 1, int $perPage = 15): Paginator
    {
        $total = self::repo()->countLoginStats($filters);
        $offset = ($page - 1) * $perPage;
        $items = self::repo()->getLoginStatsPaginated($filters, $perPage, $offset);

        return new Paginator($items, $total, $page, $perPage);
    }

    /**
     * Delete logs older than a given date.
     */
    public static function deleteOlderThan(string $date): int
    {
        return self::repo()->deleteOlderThan($date);
    }

    /**
     * Export all logs matching filters as CSV string.
     *
     * @param array<string, mixed> $filters
     */
    public static function exportCsv(array $filters): string
    {
        $logs = self::repo()->getAllLogs($filters);

        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['ID', 'User ID', 'IP', 'Device', 'Last Log', 'Created At']);

        foreach ($logs as $log) {
            fputcsv($output, [
                $log['id'],
                $log['user_id'],
                $log['indirizzo'],
                $log['device'],
                $log['last_log'],
                $log['created_at'] ?? '',
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Get distinct devices for filter dropdown.
     *
     * @return array<int, string>
     */
    public static function getDistinctDevices(): array
    {
        return self::repo()->getDistinctDevices();
    }
}
