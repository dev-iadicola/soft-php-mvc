<?php
namespace App\Core\Design;

class Stilize
{
    /**
     * 
     * //@deprecated non servirà più in futuro siccome il debug sarà un rendering di un reale file.
     * @param string $path
     * @return void
     */
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