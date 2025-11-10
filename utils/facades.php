<?php

use App\Core\Mvc;
use App\Core\Facade\Auth;
use App\Core\Facade\View;
use App\Core\Http\Request;
use App\Utils\Enviroment;
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


if (! function_exists(function: 'env')) {
    /**
     * Retrieve an environment variable with automatic type casting.
     *
     * This global helper provides a shortcut to Enviroment::get(), allowing
     * easy access to .env values anywhere in the application.
     *
     * @param string $key     The environment variable name.
     * @param mixed  $default The default value returned if not defined.
     * @return mixed          The normalized environment value.
     */
    function env(string $key, mixed $defaul = null): mixed
    {
       return Enviroment::get($key, $defaul);
    }
}


if(!function_exists('request')){
    function request(){
        return mvc()->request;
    }
}