<?php

namespace App\Core\Services;

class FolderApp
{

    static array $folders;

    public function __get($name)
    {
        return self::$folders[$name];
    }
    public static function set(string $key, string|array $root = '')
    {
        if (is_array($root))
            foreach ($root as $key => $subrot) {
                self::$folders[$key] = baseRoot() . $subrot;
            }
        else
            self::$folders[$key] = baseRoot() . $root;
    }

}
