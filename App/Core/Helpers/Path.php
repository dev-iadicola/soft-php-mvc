<?php

namespace App\Core\Helpers;

class Path
{
  public static function normalize(string $path): string
  {

    return  $pathNormalized = str_replace(["/", "\\", '//'], DIRECTORY_SEPARATOR, $path);
  }
  public static function root(string $path): string
  {
    if (!preg_match('#^[\\\\/]#', $path)) {
      $path = DIRECTORY_SEPARATOR . $path;
    }
    return self::normalize(baseRoot() . $path);
  }

  public static function convertDotToSlash(string $dir):string{
    return str_replace('.', DIRECTORY_SEPARATOR, $dir);
  }
}
