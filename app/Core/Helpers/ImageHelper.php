<?php

declare(strict_types=1);

namespace App\Core\Helpers;

class ImageHelper
{
    private const MAX_WIDTH = 1200;
    private const MAX_HEIGHT = 1200;
    private const JPEG_QUALITY = 80;
    private const PNG_COMPRESSION = 6;

    /**
     * Resize and compress an image from its binary content.
     * Returns the processed image as a binary string.
     */
    public static function processFromString(string $content, string $extension, int $maxWidth = self::MAX_WIDTH, int $maxHeight = self::MAX_HEIGHT): string
    {
        $image = @imagecreatefromstring($content);

        if ($image === false) {
            return $content;
        }

        $origWidth = imagesx($image);
        $origHeight = imagesy($image);

        if ($origWidth > $maxWidth || $origHeight > $maxHeight) {
            $image = self::resizeImage($image, $origWidth, $origHeight, $maxWidth, $maxHeight);
        }

        $result = self::imageToString($image, $extension);

        imagedestroy($image);

        return $result;
    }

    /**
     * Resize an image file on disk in place.
     */
    public static function resize(string $sourcePath, int $maxWidth = self::MAX_WIDTH, int $maxHeight = self::MAX_HEIGHT): bool
    {
        if (!file_exists($sourcePath)) {
            return false;
        }

        $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $content = file_get_contents($sourcePath);

        if ($content === false) {
            return false;
        }

        $processed = self::processFromString($content, $extension, $maxWidth, $maxHeight);
        file_put_contents($sourcePath, $processed);

        return true;
    }

    /**
     * Compress an image file on disk in place.
     */
    public static function compress(string $sourcePath, int $quality = self::JPEG_QUALITY): bool
    {
        if (!file_exists($sourcePath)) {
            return false;
        }

        $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $image = self::createFromFile($sourcePath, $extension);

        if ($image === null) {
            return false;
        }

        match ($extension) {
            'jpg', 'jpeg' => imagejpeg($image, $sourcePath, $quality),
            'png' => imagepng($image, $sourcePath, (int) round((100 - $quality) / 10)),
            'webp' => imagewebp($image, $sourcePath, $quality),
            default => false,
        };

        imagedestroy($image);

        return true;
    }

    private static function resizeImage(\GdImage $image, int $origWidth, int $origHeight, int $maxWidth, int $maxHeight): \GdImage
    {
        $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
        $newWidth = (int) round($origWidth * $ratio);
        $newHeight = (int) round($origHeight * $ratio);

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG/WebP
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        return $resized;
    }

    private static function imageToString(\GdImage $image, string $extension): string
    {
        ob_start();

        match ($extension) {
            'jpg', 'jpeg' => imagejpeg($image, null, self::JPEG_QUALITY),
            'png' => imagepng($image, null, self::PNG_COMPRESSION),
            'webp' => imagewebp($image, null, self::JPEG_QUALITY),
            default => imagepng($image),
        };

        return ob_get_clean() ?: '';
    }

    private static function createFromFile(string $path, string $extension): ?\GdImage
    {
        $image = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'webp' => @imagecreatefromwebp($path),
            default => false,
        };

        return $image !== false ? $image : null;
    }
}
