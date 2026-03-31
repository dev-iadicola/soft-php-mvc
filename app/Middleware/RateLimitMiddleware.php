<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Http\Request;
use App\Core\Contract\MiddlewareInterface;
use App\Services\RateLimitService;

/**
 * Summary of RateLimitMiddleware
 * Middleware that handles the maximum number of request made with APIs only
 */
class RateLimitMiddleware implements MiddlewareInterface
{
    public function exec(Request $request): mixed
    {
        $config = mvc()->config->get('settings.rate_limit');
        $path = rtrim($request->path(), '/');
        $path = $path === '' ? '/' : $path;
        $routeKey = strtoupper($request->method()) . ' ' . $path;
        $routeConfig = $config['routes'][$routeKey] ?? $config['default'] ?? ['max' => 5, 'window' => 900];
        $maxRequest = (int) ($routeConfig['max'] ?? 5);
        $window = (int) ($routeConfig['window'] ?? 900);

        $result = RateLimitService::hit(
            $request->getIp(),
            $routeKey,
            $maxRequest,
            $window
        );

        if ($result['allowed'] === false) {
            $message = sprintf(
                'Troppi tentativi. Riprova tra %d secondi.',
                max(1, (int) $result['retry_after'])
            );

            if (response()->wantsJson()) {
                return response()->json(
                    [
                        'error' => $message,
                        'limit' => $maxRequest,
                        'window' => $window,
                        'retry_after' => $result['retry_after'],
                    ],
                    429
                );
            }

            return response()->back(429)->withError($message);
        }

        return null;
    }
}
