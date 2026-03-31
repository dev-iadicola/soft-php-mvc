<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::rawSql(
    [
        "ALTER TABLE `projects` ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'in_progress' AFTER `is_active`",
        "ALTER TABLE `projects` ADD COLUMN `started_at` DATE NULL DEFAULT NULL AFTER `status`",
        "ALTER TABLE `projects` ADD COLUMN `ended_at` DATE NULL DEFAULT NULL AFTER `started_at`",
    ],
    [
        "ALTER TABLE `projects` DROP COLUMN `ended_at`",
        "ALTER TABLE `projects` DROP COLUMN `started_at`",
        "ALTER TABLE `projects` DROP COLUMN `status`",
    ]
);
