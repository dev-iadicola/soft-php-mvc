<?php

use App\Core\Helpers\Types\StrHelper;

if (!function_exists(function: 'truncate')) {
 function truncate(null|string $text = null, ?int $limit = 50, null|string $prefix = '...'): string{
    return StrHelper::truncate($text, $limit, $prefix);
 }
}