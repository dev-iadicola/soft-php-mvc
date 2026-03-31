<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\RateLimit;

class RateLimitService
{
    /**
     * @return array{allowed: bool, attempts: int, retry_after: int}
     */
    public static function hit(string $ip, string $route, int $maxAttempts, int $windowSeconds): array
    {
        $now = time();
        $timestamp = date('Y-m-d H:i:s', $now);

        /** @var RateLimit|null $rateLimit */
        $rateLimit = RateLimit::query()
            ->where('ip', $ip)
            ->where('route', $route)
            ->first();

        if (!$rateLimit instanceof RateLimit) {
            RateLimit::query()->create([
                'ip' => $ip,
                'route' => $route,
                'attempts' => 1,
                'last_attempt_at' => $timestamp,
            ]);

            return [
                'allowed' => true,
                'attempts' => 1,
                'retry_after' => 0,
            ];
        }

        $lastAttemptAt = strtotime($rateLimit->last_attempt_at);
        $elapsed = $lastAttemptAt === false ? $windowSeconds + 1 : ($now - $lastAttemptAt);

        if ($elapsed > $windowSeconds) {
            RateLimit::query()->where('id', $rateLimit->id)->update([
                'attempts' => 1,
                'last_attempt_at' => $timestamp,
            ]);

            return [
                'allowed' => true,
                'attempts' => 1,
                'retry_after' => 0,
            ];
        }

        $attempts = (int) $rateLimit->attempts + 1;
        RateLimit::query()->where('id', $rateLimit->id)->update([
            'attempts' => $attempts,
            'last_attempt_at' => $timestamp,
        ]);

        return [
            'allowed' => $attempts <= $maxAttempts,
            'attempts' => $attempts,
            'retry_after' => max(0, $windowSeconds - $elapsed),
        ];
    }
}
