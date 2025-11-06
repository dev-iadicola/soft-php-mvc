<?php

use App\Core\Mvc;
use App\Core\Facade\Auth;
use App\Core\Facade\View;
use App\Core\Services\AuthService;
use App\Core\Support\Collection\BuildAppFile;

/**
 * facade for Auth
 */
if (!function_exists(function: 'auth')) {
    function auth(): AuthService
    {
        return Auth::getInstance();
    }
}
// TODO: fare facade sia per Route che per View(migliorando prima la struttura base) 

if (!function_exists(function: 'view')) {
    function view(string $page, array $variables = [], array|null $message = null)
    {
        return View::make($page, $variables, $message);
    }
}


/**
 * Setta l'mvc rendendolo globale
 */


if (!function_exists('mvc')) {
    /**
     * Summary of mvc
     * This function allows to access the MVC istance, which is important and necessary for many framework operations
     * @return Mvc
     */
    function mvc(): Mvc|null
    {
     
        return Mvc::$mvc ?? null;
    }
}
