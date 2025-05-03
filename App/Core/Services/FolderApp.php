<?php

namespace App\Core\Services;

class FolderApp
{

    static array $folders;

    public function __get($name)
    {
        return self::$folders[$name];
    }
    public static function set(string $key, string $root = '')
    {
        if (str_contains($root,'.'))
            self::$folders[$key] = convertDotToSlash($root);
        else
            self::$folders[$key] = baseRoot() . $root;
    }

}
