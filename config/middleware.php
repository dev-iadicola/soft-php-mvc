<?php
/**
 * File per l'autenticazione dell'utente
 * gestione tramite Array 
 */

use App\Middleware\AdminMiddleware;
use App\Middleware\MaintenanceMiddleware;

return [
    '/' => [MaintenanceMiddleware::class],

    '/admin' => [AdminMiddleware::class],
  
];