<?php 
namespace App\Core\Helpers;
class Path {
    public static function normalize(string $path): string{

      return  $pathNormalized = str_replace(["/", "\\", '//'], DIRECTORY_SEPARATOR, $path);
    }
}