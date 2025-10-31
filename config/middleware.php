<?php
/**
 * File per l'autenticazione dell'utente
 * gestione tramite Array 
 */

use App\Middleware\AuthMiddleware;
use App\Middleware\MaintenanceMiddleware;

return [
    'web' => [MaintenanceMiddleware::class],
    'auth' => [AuthMiddleware::class]
];