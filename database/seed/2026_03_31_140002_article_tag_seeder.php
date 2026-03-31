<?php

declare(strict_types=1);

use App\Core\DataLayer\Seeder\Seeder;
use App\Core\Database;
use App\Core\Helpers\Str;

require_once __DIR__ . '/stubs/article-demo-content.php';

$pdo = Database::getInstance()->getConnection();
$articleStatement = $pdo->prepare('SELECT id FROM articles WHERE slug = ? LIMIT 1');
$tagStatement = $pdo->prepare('SELECT id FROM tags WHERE slug = ? LIMIT 1');

$seeder = Seeder::table('article_tag');

foreach (articleDemoDefinitions() as $article) {
    $articleStatement->execute([$article['slug']]);
    $articleId = $articleStatement->fetchColumn();

    if ($articleId === false) {
        continue;
    }

    foreach ($article['tags'] as $tagName) {
        $tagStatement->execute([Str::slug($tagName)]);
        $tagId = $tagStatement->fetchColumn();

        if ($tagId === false) {
            continue;
        }

        $seeder->row([
            'article_id' => (int) $articleId,
            'tag_id' => (int) $tagId,
        ]);
    }
}

return $seeder;
