<?php
if (!function_exists(function: 'view')) {
    function view(string $page, array $variables = [], array|null $message = null)
    {
        return mvc()->controller->render($page, $variables, $message);
    }
}

