<?php 

use App\Core\Http\Response;

if (!function_exists(function: 'redirect')) {
    function redirect(?string $url = null, $code = 302): Response
    {
        return  mvc()->response->redirect($url, $code);
    }
}

if (!function_exists(function: 'response')) {
    function response(): Response{
        return mvc()->response;
    }
}
