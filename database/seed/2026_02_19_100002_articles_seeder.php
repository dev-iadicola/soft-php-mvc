<?php

declare(strict_types=1);

use App\Core\DataLayer\Seeder\Seeder;

require_once __DIR__ . '/stubs/article-demo-content.php';

$seeder = Seeder::table('articles');

foreach (articleDemoDefinitions() as $article) {
    $seeder->row([
        'title' => $article['title'],
        'slug' => $article['slug'],
        'subtitle' => $article['subtitle'],
        'overview' => $article['overview'],
        'img' => articleDemoCover($article['cover_label'], $article['accent'], $article['background']),
        'link' => $article['link'],
        'is_active' => 1,
        'created_at' => $article['created_at'],
    ]);
}

return $seeder;
