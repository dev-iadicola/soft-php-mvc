<?php

use App\Core\DataLayer\Migration\Migration;

$tables = ['projects', 'technology', 'partners', 'links_footer'];

$up = [];
$down = [];

foreach ($tables as $table) {
    $up[] = "ALTER TABLE `{$table}` ADD COLUMN `sort_order` INT NOT NULL DEFAULT 0";
    $down[] = "ALTER TABLE `{$table}` DROP COLUMN `sort_order`";
}

return Migration::rawSql($up, $down);
