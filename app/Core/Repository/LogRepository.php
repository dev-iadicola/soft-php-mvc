<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Core\DataLayer\Table;
use App\Model\LogTrace;

class LogRepository extends BaseRepository
{
    private Table $table;

    public function __construct()
    {
        parent::__construct(LogTrace::class);
        $this->table = new Table('logs');
    }

    /**
     * Get paginated login stats with optional filters.
     *
     * @param array<string, mixed> $filters  Keys: date_from, date_to, user_id, device
     * @return array<int, array<string, mixed>>
     */
    public function getLoginStatsPaginated(array $filters, int $limit, int $offset): array
    {
        [$where, $params] = $this->buildFilterClauses($filters);

        $sql = "SELECT indirizzo, device, COUNT(*) AS login_count, MAX(last_log) AS last_log, user_id
                FROM logs"
            . ($where !== '' ? " WHERE {$where}" : '')
            . " GROUP BY indirizzo, device, user_id
                ORDER BY last_log DESC
                LIMIT :limit OFFSET :offset";

        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        return $this->table->fetchAll($sql, $params);
    }

    /**
     * Count distinct groups for pagination.
     *
     * @param array<string, mixed> $filters
     */
    public function countLoginStats(array $filters): int
    {
        [$where, $params] = $this->buildFilterClauses($filters);

        $sql = "SELECT COUNT(*) FROM (
                    SELECT 1 FROM logs"
            . ($where !== '' ? " WHERE {$where}" : '')
            . " GROUP BY indirizzo, device, user_id
                ) AS sub";

        return (int) $this->table->scalar($sql, $params);
    }

    /**
     * Get all logs matching filters (for CSV export).
     *
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function getAllLogs(array $filters): array
    {
        [$where, $params] = $this->buildFilterClauses($filters);

        $sql = "SELECT id, user_id, indirizzo, device, last_log, created_at
                FROM logs"
            . ($where !== '' ? " WHERE {$where}" : '')
            . " ORDER BY last_log DESC";

        return $this->table->fetchAll($sql, $params);
    }

    /**
     * Delete logs older than a given date.
     */
    public function deleteOlderThan(string $date): int
    {
        $sql = "DELETE FROM logs WHERE last_log < :date";
        $stmt = $this->table->fetchAll("SELECT COUNT(*) as cnt FROM logs WHERE last_log < :date", [':date' => $date]);
        $count = (int) ($stmt[0]['cnt'] ?? 0);

        if ($count > 0) {
            // Use Table's fetchAll with a DELETE won't work, use PDO directly
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $s = $pdo->prepare($sql);
            $s->bindValue(':date', $date);
            $s->execute();
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
        $rows = $this->table->fetchAll("SELECT DISTINCT device FROM logs ORDER BY device");
        return array_column($rows, 'device');
    }

    /**
     * Build WHERE clause and params from filters.
     *
     * @param array<string, mixed> $filters
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function buildFilterClauses(array $filters): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['date_from'])) {
            $conditions[] = "last_log >= :date_from";
            $params[':date_from'] = $filters['date_from'] . ' 00:00:00';
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = "last_log <= :date_to";
            $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
        }

        if (!empty($filters['user_id'])) {
            $conditions[] = "user_id = :user_id";
            $params[':user_id'] = (int) $filters['user_id'];
        }

        if (!empty($filters['device'])) {
            $conditions[] = "device LIKE :device";
            $params[':device'] = '%' . $filters['device'] . '%';
        }

        $where = implode(' AND ', $conditions);

        return [$where, $params];
    }
}
