<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Model\Visitor;

class VisitorRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Visitor::class);
    }

    /**
     * @return array<int, Visitor>
     */
    public function getRecent(int $limit = 50): array
    {
        /** @var array<int, Visitor> */
        return $this->query()
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function getTotalVisits(): int
    {
        return $this->query()->count();
    }

    public function getTodayVisits(): int
    {
        return $this->query()
            ->where('created_at', '>=', date('Y-m-d 00:00:00'))
            ->count();
    }

    public function getUniqueVisitors(): int
    {
        return $this->query()->countDistinct('ip_address');
    }

    public function getTodayUniqueVisitors(): int
    {
        return $this->query()
            ->where('created_at', '>=', date('Y-m-d 00:00:00'))
            ->countDistinct('ip_address');
    }

    /**
     * @return array<int, array{date: string, count: int}>
     */
    public function getVisitsByDay(int $days = 30): array
    {
        return $this->query()
            ->selectRaw('DATE(created_at) AS date')
            ->selectAggregate('COUNT', '*', 'count')
            ->where('created_at', '>=', date('Y-m-d', strtotime("-{$days} days")))
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->fetchRows();
    }

    /**
     * @return array<int, array{week: string, count: int}>
     */
    public function getVisitsByWeek(int $weeks = 12): array
    {
        $days = $weeks * 7;

        return $this->query()
            ->selectRaw('YEARWEEK(created_at, 1) AS week')
            ->selectAggregate('COUNT', '*', 'count')
            ->where('created_at', '>=', date('Y-m-d', strtotime("-{$days} days")))
            ->groupByRaw('YEARWEEK(created_at, 1)')
            ->orderBy('week')
            ->fetchRows();
    }

    /**
     * @return array<int, array{month: string, count: int}>
     */
    public function getVisitsByMonth(int $months = 12): array
    {
        return $this->query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") AS month')
            ->selectAggregate('COUNT', '*', 'count')
            ->where('created_at', '>=', date('Y-m-01', strtotime("-{$months} months")))
            ->groupByRaw('DATE_FORMAT(created_at, "%Y-%m")')
            ->orderBy('month')
            ->fetchRows();
    }

    /**
     * @return array<int, array{user_agent: string, count: int}>
     */
    public function getUserAgentCounts(int $limit = 100): array
    {
        return $this->query()
            ->select('user_agent')
            ->selectAggregate('COUNT', '*', 'count')
            ->whereNotNull('user_agent')
            ->groupBy('user_agent')
            ->orderBy('count', 'DESC')
            ->limit($limit)
            ->fetchRows();
    }

    /**
     * @return array<int, array{url: string, count: int}>
     */
    public function getTopPages(int $limit = 10): array
    {
        return $this->query()
            ->select('url')
            ->selectAggregate('COUNT', '*', 'count')
            ->whereNotNull('url')
            ->groupBy('url')
            ->orderBy('count', 'DESC')
            ->limit($limit)
            ->fetchRows();
    }
}
