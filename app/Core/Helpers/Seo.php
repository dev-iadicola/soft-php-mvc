<?php

declare(strict_types=1);

namespace App\Core\Helpers;

class Seo
{
    private const DEFAULT_TITLE = 'Iadicola // dev';
    private const DEFAULT_DESCRIPTION = 'Portfolio e progetti di sviluppo web - PHP, Laravel, React e altre tecnologie.';

    /**
     * Build SEO meta array for views.
     *
     * @param array{title?: string, description?: string, image?: string, url?: string} $data
     * @return array{title: string, description: string, image: string, url: string}
     */
    public static function make(array $data = []): array
    {
        $baseUrl = self::baseUrl();

        return [
            'title' => isset($data['title']) ? $data['title'] . ' | ' . self::DEFAULT_TITLE : self::DEFAULT_TITLE,
            'description' => $data['description'] ?? self::DEFAULT_DESCRIPTION,
            'image' => $data['image'] ?? $baseUrl . '/assets/img/favicon.png',
            'url' => $data['url'] ?? $baseUrl . ($_SERVER['REQUEST_URI'] ?? '/'),
        ];
    }

    public static function baseUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $scheme . '://' . $host;
    }
}
