<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Core\DataLayer\Query\ActiveQuery;
use App\Model\LogTrace;

class LogRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(LogTrace::class);
    }

    /**
     * Get paginated login stats with optional filters.
     *
     * @param array<string, mixed> $filters  Keys: date_from, date_to, user_id, device
     * @return array<int, array<string, mixed>>
     */
    public function getLoginStatsPaginated(array $filters, int $limit, int $offset): array
    {
        $q = $this->query()
            ->select(['indirizzo', 'device', 'user_id'])
            ->selectAggregate('COUNT', '*', 'login_count')
            ->selectAggregate('MAX', 'last_log', 'last_log');

        $this->applyFilters($q, $filters);

        return $q->groupBy(['indirizzo', 'device', 'user_id'])
            ->orderBy('last_log', 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->fetchRows();
    }

    /**
     * Count distinct groups for pagination.
     *
     * @param array<string, mixed> $filters
     */
    public function countLoginStats(array $filters): int
    {
        $q = $this->query()
            ->select('1')
            ->groupBy(['indirizzo', 'device', 'user_id']);

        $this->applyFilters($q, $filters);

        return count($q->fetchRows());
    }

    /**
     * Get all logs matching filters (for CSV export).
     *
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function getAllLogs(array $filters): array
    {
        $q = $this->query()
            ->select(['id', 'user_id', 'indirizzo', 'device', 'last_log', 'created_at']);

        $this->applyFilters($q, $filters);

        return $q->orderBy('last_log', 'DESC')
            ->fetchRows();
    }

    /**
     * Delete logs older than a given date.
     */
    public function deleteOlderThan(string $date): int
    {
        $count = $this->query()
            ->where('last_log', '<', $date)
            ->count();

        if ($count > 0) {
            $this->query()
                ->where('last_log', '<', $date)
                ->delete();
        }

        return $count;
    }

    /**
     * Get distinct devices for filter dropdown.
     *
     * @return array<int, string>
     */
    public function getDistinctDevices(): array
    {
        $rows = $this->query()
            ->distinct()
            ->select('device')
            ->orderBy('device')
            ->fetchRows();

        return array_column($rows, 'device');
    }

    /**
     * Apply common filters to a query.
     *
     * @param array<string, mixed> $filters
     */
    private function applyFilters(ActiveQuery $q, array $filters): void
    {
        if (!empty($filters['date_from'])) {
            $q->where('last_log', '>=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $q->where('last_log', '<=', $filters['date_to'] . ' 23:59:59');
        }

        if (!empty($filters['user_id'])) {
            $q->where('user_id', (int) $filters['user_id']);
        }

        if (!empty($filters['device'])) {
            $q->where('device', 'LIKE', '%' . $filters['device'] . '%');
        }
    }
}
