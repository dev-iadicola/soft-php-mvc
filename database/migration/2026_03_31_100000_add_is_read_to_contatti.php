<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::rawSql(
    ["ALTER TABLE `contatti` ADD COLUMN `is_read` TINYINT(1) NOT NULL DEFAULT 0 AFTER `typologie`"],
    ["ALTER TABLE `contatti` DROP COLUMN `is_read`"]
);
