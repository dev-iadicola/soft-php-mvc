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
     // Middlewares for all web routes (html, forms, etc.)
     // Global Middleware, use for all request, exception for route has 'api'
    'web' => [
        SecureHeaderMiddleware::class,
        CsrfMiddleware::class,
        MethodOverrideMiddleware::class,
        RateLimitMiddleware::class,
    ],
    // Middlwares for APIs
    'api' => [
        CorsMiddleware::class,
        RateLimitMiddleware::class
    ],
    // Middleware for maintenance
    'guest' => [MaintenanceMiddleware::class],
    // Middleware for user auth.
    'auth' => [AuthMiddleware::class]
];
