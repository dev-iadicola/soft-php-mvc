<?php

use App\Core\DataLayer\Migration\Migration;

$tables = ['projects', 'skills', 'articles', 'technology', 'partners', 'corsi'];

$up = [];
$down = [];

foreach ($tables as $table) {
    $up[] = "ALTER TABLE `{$table}` ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1";
    $down[] = "ALTER TABLE `{$table}` DROP COLUMN `is_active`";
}

return Migration::rawSql($up, $down);
