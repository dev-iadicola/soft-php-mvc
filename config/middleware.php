<?php

/**
 * File per l'autenticazione dell'utente
 * gestione tramite Array 
 */

use App\Middleware\AuthMiddleware;
use App\Middleware\MaintenanceMiddleware;
use App\Middleware\MethodOverrideMiddleware;

return [
    'web' => [MethodOverrideMiddleware::class],
    'guest' => [MaintenanceMiddleware::class],
    'auth' => [AuthMiddleware::class]
];
