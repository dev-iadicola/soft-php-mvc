<?php

namespace App\Middleware;

use App\Core\Http\Request;
use App\Core\Facade\Session;
use App\Core\Contract\MiddlewareInterface;

/**
 * Summary of RateLimitMiddleware
 * Middleware that handles the maximum number of request made with APIs only
 */
class RateLimitMiddleware implements MiddlewareInterface
{
    private int $maxRequest;
    private int $window;
    public function exec(Request $request)
    {   // I take the maximum limit of request allowed.
        $this->maxRequest = mvc()->config->settings['request']['max'];
        // I take the time window for this limit.
        $this->window = mvc()->config->settings['request']['window'];

        $count = Session::getOrCreate('request_count', 0);
        $start = Session::getOrCreate('request_start_time', time());
      
        // If the time windoes has exprired, reset it.
        if ((time() - $start) > $this->window) {
            $count = 0;
            $start = time();
            Session::create([
                'request_count' => $count,
                'request_start_time' => $start
            ]);
        }

        // check numebrs of requests don't exceed the limit of the max requests
        $count++;
        Session::set('request_count', $count);

        if ($count > $this->maxRequest) {
            response()->json(
                [
                    'error' => 'Too many requests',
                    'limit' => $this->maxRequest,
                    'window' => "{$this->window} seconds",
                ],
                429
            );
        }
    }
}
