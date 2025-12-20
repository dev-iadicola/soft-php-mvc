<?php

declare(strict_types=1);

namespace App\Core\Helpers\Types;

class StrHelper
{
    public static function truncate(null|string $text = null, ?int $limit = 50, null|string $suffix = '...'): string
    {
        if($text === null){
            return "";
        }
        return mb_strlen($text) > $limit
            ? mb_substr($text, 0, $limit) . $suffix
            : $text;
    }
}
