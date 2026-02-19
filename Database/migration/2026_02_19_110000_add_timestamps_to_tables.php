<?php

use App\Core\DataLayer\Migration\Migration;
use App\Core\Database;

$tables = [
    'users',
    'technology',
    'partners',
    'articles',
    'contatti',
    'corsi',
    'curriculum',
    'laws',
    'links_footer',
    'portfolio',
    'profile',
    'skills',
    'visitors',
    'logs',
    'projects',
    'tokens',
    'project_technologies',
];

$up = [];
$down = [];

// Build idempotent SQL based on current schema
$pdo = Database::getInstance()->getConnection();
$dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
$checkStmt = $pdo->prepare(
    'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?'
);

foreach ($tables as $table) {
    $checkStmt->execute([$dbName, $table, 'created_at']);
    $hasCreated = (int)$checkStmt->fetchColumn() > 0;

    $checkStmt->execute([$dbName, $table, 'updated_at']);
    $hasUpdated = (int)$checkStmt->fetchColumn() > 0;

    if (!$hasCreated) {
        $up[] = "ALTER TABLE `$table` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    }
    if (!$hasUpdated) {
        $up[] = "ALTER TABLE `$table` ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL";
    }

    if ($hasUpdated) {
        $down[] = "ALTER TABLE `$table` DROP COLUMN `updated_at`";
    }
    if ($hasCreated) {
        $down[] = "ALTER TABLE `$table` DROP COLUMN `created_at`";
    }
}

return Migration::rawSql($up, $down);
