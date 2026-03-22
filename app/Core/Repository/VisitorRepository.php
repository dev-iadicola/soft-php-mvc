<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Core\DataLayer\Table;
use App\Model\Visitor;

class VisitorRepository extends BaseRepository
{
    private Table $table;

    public function __construct()
    {
        parent::__construct(Visitor::class);
        $this->table = new Table('visitors');
    }

    /**
     * @return array<int, Visitor>
     */
    public function getRecent(int $limit = 50): array
    {
        /** @var array<int, Visitor> */
        return $this->query()->query(
            'SELECT * FROM visitors ORDER BY created_at DESC LIMIT :limit',
            [':limit' => $limit]
        );
    }

    public function getTotalVisits(): int
    {
        return $this->table->count();
    }

    public function getTodayVisits(): int
    {
        return $this->table->count('created_at >= :today', [':today' => date('Y-m-d 00:00:00')]);
    }

    public function getUniqueVisitors(): int
    {
        return $this->table->countDistinct('ip_address');
    }

    public function getTodayUniqueVisitors(): int
    {
        return $this->table->countDistinct('ip_address', 'created_at >= :today', [':today' => date('Y-m-d 00:00:00')]);
    }

    /**
     * @return array<int, array{date: string, count: int}>
     */
    public function getVisitsByDay(int $days = 30): array
    {
        return $this->table->fetchAll(
            'SELECT DATE(created_at) AS date, COUNT(*) AS count
             FROM visitors
             WHERE created_at >= :since
             GROUP BY DATE(created_at)
             ORDER BY date ASC',
            [':since' => date('Y-m-d', strtotime("-{$days} days"))]
        );
    }

    /**
     * @return array<int, array{week: string, count: int}>
     */
    public function getVisitsByWeek(int $weeks = 12): array
    {
        $days = $weeks * 7;

        return $this->table->fetchAll(
            'SELECT YEARWEEK(created_at, 1) AS week, COUNT(*) AS count
             FROM visitors
             WHERE created_at >= :since
             GROUP BY YEARWEEK(created_at, 1)
             ORDER BY week ASC',
            [':since' => date('Y-m-d', strtotime("-{$days} days"))]
        );
    }

    /**
     * @return array<int, array{month: string, count: int}>
     */
    public function getVisitsByMonth(int $months = 12): array
    {
        return $this->table->fetchAll(
            'SELECT DATE_FORMAT(created_at, "%Y-%m") AS month, COUNT(*) AS count
             FROM visitors
             WHERE created_at >= :since
             GROUP BY DATE_FORMAT(created_at, "%Y-%m")
             ORDER BY month ASC',
            [':since' => date('Y-m-01', strtotime("-{$months} months"))]
        );
    }

    /**
     * @return array<int, array{user_agent: string, count: int}>
     */
    public function getUserAgentCounts(int $limit = 100): array
    {
        return $this->table->fetchAll(
            'SELECT user_agent, COUNT(*) AS count
             FROM visitors
             WHERE user_agent IS NOT NULL
             GROUP BY user_agent
             ORDER BY count DESC
             LIMIT ' . $limit
        );
    }

    /**
     * @return array<int, array{url: string, count: int}>
     */
    public function getTopPages(int $limit = 10): array
    {
        return $this->table->fetchAll(
            'SELECT url, COUNT(*) AS count
             FROM visitors
             WHERE url IS NOT NULL
             GROUP BY url
             ORDER BY count DESC
             LIMIT ' . $limit
        );
    }
}
