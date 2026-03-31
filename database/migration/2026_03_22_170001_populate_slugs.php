<?php

use App\Core\DataLayer\Migration\Migration;
use App\Core\Helpers\Str;

// Populate slugs for existing projects and articles from their titles.
// This runs as raw SQL with generated values.

$up = function (): array {
    $pdo = \App\Core\Database::getInstance()->getConnection();
    $statements = [];

    // Projects
    $rows = $pdo->query("SELECT id, title FROM projects WHERE slug IS NULL")->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $slug = Str::slug($row['title']);
        $statements[] = "UPDATE projects SET slug = " . $pdo->quote($slug) . " WHERE id = " . (int) $row['id'];
    }

    // Articles
    $rows = $pdo->query("SELECT id, title FROM articles WHERE slug IS NULL")->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $slug = Str::slug($row['title']);
        $statements[] = "UPDATE articles SET slug = " . $pdo->quote($slug) . " WHERE id = " . (int) $row['id'];
    }

    return $statements;
};

return Migration::rawSql($up(), [
    "UPDATE projects SET slug = NULL",
    "UPDATE articles SET slug = NULL",
]);
