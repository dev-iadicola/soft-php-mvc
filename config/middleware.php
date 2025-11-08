<?php

/**
 * File per l'autenticazione dell'utente
 * gestione tramite Array 
 */

use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\MaintenanceMiddleware;
use App\Middleware\MethodOverrideMiddleware;
use App\Middleware\RateLimitMiddleware;
use App\Middleware\SecureHeaderMiddleware;

return [
    'web' => [
        MethodOverrideMiddleware::class,
        CsrfMiddleware::class,
        RateLimitMiddleware::class,
        SecureHeaderMiddleware::class
    ],
    'api' => [CorsMiddleware::class, RateLimitMiddleware::class],
    'guest' => [MaintenanceMiddleware::class],
    'auth' => [AuthMiddleware::class]
];
