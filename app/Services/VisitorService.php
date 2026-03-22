<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Repository\VisitorRepository;
use App\Model\Visitor;

class VisitorService
{
    private static ?VisitorRepository $repository = null;

    private static function repository(): VisitorRepository
    {
        if (self::$repository === null) {
            self::$repository = new VisitorRepository();
        }

        return self::$repository;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function create(array $data): void
    {
        self::repository()->create($data);
    }

    /**
     * @return array<int, Visitor>
     */
    public static function getRecent(int $limit = 50): array
    {
        return self::repository()->getRecent($limit);
    }

    public static function getTotalVisits(): int
    {
        return self::repository()->getTotalVisits();
    }

    public static function getTodayVisits(): int
    {
        return self::repository()->getTodayVisits();
    }

    public static function getUniqueVisitors(): int
    {
        return self::repository()->getUniqueVisitors();
    }

    public static function getTodayUniqueVisitors(): int
    {
        return self::repository()->getTodayUniqueVisitors();
    }

    /**
     * @return array<int, array{date: string, count: int}>
     */
    public static function getVisitsByDay(int $days = 30): array
    {
        return self::repository()->getVisitsByDay($days);
    }

    /**
     * @return array<int, array{week: string, count: int}>
     */
    public static function getVisitsByWeek(int $weeks = 12): array
    {
        return self::repository()->getVisitsByWeek($weeks);
    }

    /**
     * @return array<int, array{month: string, count: int}>
     */
    public static function getVisitsByMonth(int $months = 12): array
    {
        return self::repository()->getVisitsByMonth($months);
    }

    /**
     * Top browser (estratti da user_agent).
     *
     * @return array<int, array{browser: string, count: int}>
     */
    public static function getTopBrowsers(int $limit = 10): array
    {
        $rows = self::repository()->getUserAgentCounts($limit * 5);

        $browsers = [];
        foreach ($rows as $row) {
            $browser = self::parseBrowser((string) $row['user_agent']);
            $browsers[$browser] = ($browsers[$browser] ?? 0) + (int) $row['count'];
        }

        arsort($browsers);

        $result = [];
        foreach ($browsers as $browser => $count) {
            $result[] = ['browser' => $browser, 'count' => $count];
        }

        return array_slice($result, 0, $limit);
    }

    /**
     * Top dispositivi (desktop/mobile/tablet).
     *
     * @return array<int, array{device: string, count: int}>
     */
    public static function getTopDevices(): array
    {
        $rows = self::repository()->getUserAgentCounts(500);

        $devices = ['Desktop' => 0, 'Mobile' => 0, 'Tablet' => 0, 'Bot' => 0];

        foreach ($rows as $row) {
            $device = self::parseDevice((string) $row['user_agent']);
            $devices[$device] += (int) $row['count'];
        }

        $result = [];
        foreach ($devices as $device => $count) {
            if ($count > 0) {
                $result[] = ['device' => $device, 'count' => $count];
            }
        }

        usort($result, fn(array $a, array $b) => $b['count'] <=> $a['count']);

        return $result;
    }

    /**
     * @return array<int, array{url: string, count: int}>
     */
    public static function getTopPages(int $limit = 10): array
    {
        return self::repository()->getTopPages($limit);
    }

    private static function parseBrowser(string $userAgent): string
    {
        $ua = strtolower($userAgent);

        if (str_contains($ua, 'edg/') || str_contains($ua, 'edge/')) {
            return 'Edge';
        }
        if (str_contains($ua, 'opr/') || str_contains($ua, 'opera')) {
            return 'Opera';
        }
        if (str_contains($ua, 'chrome/') && !str_contains($ua, 'chromium')) {
            return 'Chrome';
        }
        if (str_contains($ua, 'firefox/')) {
            return 'Firefox';
        }
        if (str_contains($ua, 'safari/') && !str_contains($ua, 'chrome')) {
            return 'Safari';
        }
        if (str_contains($ua, 'msie') || str_contains($ua, 'trident/')) {
            return 'Internet Explorer';
        }

        return 'Altro';
    }

    private static function parseDevice(string $userAgent): string
    {
        $ua = strtolower($userAgent);

        if (str_contains($ua, 'bot') || str_contains($ua, 'crawler') || str_contains($ua, 'spider') || str_contains($ua, 'curl') || str_contains($ua, 'wget')) {
            return 'Bot';
        }
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'Tablet';
        }
        if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
            return 'Mobile';
        }

        return 'Desktop';
    }
}
