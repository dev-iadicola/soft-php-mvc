<?php 
namespace App\Utils;

class Date {
    public static function Rome(?string $format = 'Y-m-d H:i:s')
    {
        date_default_timezone_set('Europe/Rome');
        return date($format);

    }
}