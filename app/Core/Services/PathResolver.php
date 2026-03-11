<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\Mvc;
class PathResolver
{
    public function __construct(private readonly Mvc $mvc)
    {
    }
    static public array $folders;

    public function resolve(string $folder): string
    {
        return self::$folders[$folder];
    }
    public static function set(string $key, string $root = ''): void
    {
        if (str_contains($root,'.'))
            self::$folders[$key] = baseRoot(). '/'.convertDotToSlash($root);
        else
            self::$folders[$key] = baseRoot() .'/'. $root;
    }

    public static function all(): array{
        return self::$folders;
    }

}
