<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::rawSql(
    ["ALTER TABLE `notifications` ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`"],
    ["ALTER TABLE `notifications` DROP COLUMN `updated_at`"]
);
