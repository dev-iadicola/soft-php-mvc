<?php

use App\Core\Facade\Auth;
use App\Core\Facade\View;
use App\Core\Services\AuthService;

/**
 * facade for Auth
 */
if (!function_exists(function: 'auth')) {
    function auth(): AuthService{
        return Auth::getInstance() ;
    }
}
// TODO: fare facade sia per Route che per View(migliorando prima la struttura base) 

if (!function_exists(function: 'view')) {
    function view(string $page, array $variables = [], array|null $message = null)
    {
        return View::make($page, $variables, $message);
    }
}

