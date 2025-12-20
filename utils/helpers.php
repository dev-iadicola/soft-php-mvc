<?php

use App\Core\Helpers\Path;

use App\Core\Facade\Session;
use App\Core\Facade\Storage;
use App\Core\Connection\SMTP;
use App\Core\Facade\RouteHelper;
use App\Core\Services\CsrfService;

// File: utils/helpers.php
// Defines a global helper function available everywhere

require_once 'response.php';
require_once 'var_dumper.php';
require_once "facades.php";
require_once "types.php";



if (!function_exists(function: 'implodeMessage')) {
    function implodeMessage(array $messages)
    {
        return implode('<br><br> - ', $messages);
    }
}

if (! function_exists(function: 'csrf_token')) {
    function csrf_token(): null|string
    {
        return (new CsrfService())->getToken();
    }
}

if (!function_exists('route')) {
    function route(string $name, array $params = []): string
    {
        return RouteHelper::getByName($name, $params);
    }
}
// * Used for layout pages.
if (!function_exists(function: 'isActivePage')) {
    function isActivePage(string $menuItem, string $currentPage)
    {
        return strtolower($menuItem) == strtolower($currentPage) ? 'active' : '';
    }
}
if (!function_exists(function: 'printLn')) {
    function printLn(string $var)
    {
        passthru("php soft print $var");
    }
}

if (!function_exists(function: 'printLn')) {
    function settings()
    {
        return mvc()->config->settings;
    }
}


if (!function_exists('urlExist')) {
    /**
     * Check if a URL exists by fetching its headers
     * @param string $url
     * @return bool
     */
    function urlExist($url)
    {
        if (empty($url)) return false;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true); // non scaricare il corpo
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2); // timeout breve (2 secondi)
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // segue redirect
        curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 400;
    }
}


if (!function_exists(function: 'assets')) {
    function assets(string $file): string
    {
        return '/assets/' . $file;
    }
}
if (!function_exists('validateImagePath')) {
    function validateImagePath(string $path, string $fallback)
    {
        if (file_exists(Storage::make('public')->path($path) ))
            return $path;
        else
            return $fallback;
    }
}


if (!function_exists(function: 'css')) { //get css folder in assets folder
    /**
     * Summary of css
     * return the css path
     */
    function css()
    {
        return mvc()->config->resources['css'];
    }
}




if (!function_exists('baseRoot')) {
    /**
     * Summary of baseRoot
     * 
     * @return string rotirna semrpe la rotta di documento.
     */
    function baseRoot(): string
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }
}



/**
 * Global function by class Path
 */

if (!function_exists('basepath')) {
    function basepath(string $string)
    {
        return Path::root($string);
    }
}
if (!function_exists('storagePath')) {
    function storagePath(string $path)
    {

        return Path::normalize(baseRoot() . '/storage/' . $path);
    }
}

if (!function_exists('convertDotToSlash')) {
    function convertDotToSlash(string $dir)
    {
        return Path::convertDotToSlash($dir);
    }
}



if (!function_exists('is_octal')) {
    function is_octal(int $numebr)
    {
        return preg_match('/^0[0-7]+$/', (string) $numebr) === 1;
    }
}



if (!function_exists(function: 'smtp')) {
    function smtp(): SMTP
    {
        return new SMTP();
    }
}

/**
 * Funzioni per la gestione delle flash session (messaggi di UI)
 */

if (!function_exists(function: 'flashMessage')) {
    function flashMessage(string $key): string|null
    {
        return Session::getFlash($key);
    }
}



