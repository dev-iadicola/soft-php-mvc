<?php 

use App\Core\Http\Response;

if (!function_exists(function: 'redirect')) {
    function redirect($url, $code = 302): Response
    {
        return  mvc()->response->redirect($url, $code);
    }
}