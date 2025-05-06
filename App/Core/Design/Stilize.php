<?php
namespace App\Core\Design;

class Stilize
{

    public static function get(string $path)
    {
        $realCssPath = css();

        $webPath = self::toWebPath($realCssPath);

        $styleUrl = $webPath . '/' . $path;

        echo '<link rel="stylesheet" type="text/css" href="' . $styleUrl . '">';
    }

    protected static function toWebPath(string $absolutePath): string
    {
        $documentRoot = realpath($_SERVER['DOCUMENT_ROOT']);

        $relativePath = str_replace($documentRoot, '', $absolutePath);

        return str_replace('\\', '/', $relativePath);
    }


}