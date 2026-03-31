<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::rawSql(
    [
        "ALTER TABLE `projects` ADD COLUMN `slug` VARCHAR(150) NULL DEFAULT NULL AFTER `title`",
        "ALTER TABLE `articles` ADD COLUMN `slug` VARCHAR(150) NULL DEFAULT NULL AFTER `title`",
    ],
    [
        "ALTER TABLE `projects` DROP COLUMN `slug`",
        "ALTER TABLE `articles` DROP COLUMN `slug`",
    ]
);
