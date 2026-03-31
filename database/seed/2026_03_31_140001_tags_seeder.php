<?php

declare(strict_types=1);

use App\Core\DataLayer\Seeder\Seeder;
use App\Core\Helpers\Str;

require_once __DIR__ . '/stubs/article-demo-content.php';

$seen = [];
$seeder = Seeder::table('tags');

foreach (articleDemoDefinitions() as $article) {
    foreach ($article['tags'] as $tagName) {
        $slug = Str::slug($tagName);

        if (isset($seen[$slug])) {
            continue;
        }

        $seen[$slug] = true;
        $seeder->row([
            'name' => $tagName,
            'slug' => $slug,
        ]);
    }
}

return $seeder;
