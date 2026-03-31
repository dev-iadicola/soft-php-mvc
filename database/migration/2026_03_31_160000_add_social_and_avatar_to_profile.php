<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::rawSql(
    [
        "ALTER TABLE `profile` ADD COLUMN `github_url` VARCHAR(255) NULL DEFAULT NULL AFTER `bio`",
        "ALTER TABLE `profile` ADD COLUMN `linkedin_url` VARCHAR(255) NULL DEFAULT NULL AFTER `github_url`",
        "ALTER TABLE `profile` ADD COLUMN `twitter_url` VARCHAR(255) NULL DEFAULT NULL AFTER `linkedin_url`",
        "ALTER TABLE `profile` ADD COLUMN `avatar` VARCHAR(255) NULL DEFAULT NULL AFTER `twitter_url`",
    ],
    [
        "ALTER TABLE `profile` DROP COLUMN `avatar`",
        "ALTER TABLE `profile` DROP COLUMN `twitter_url`",
        "ALTER TABLE `profile` DROP COLUMN `linkedin_url`",
        "ALTER TABLE `profile` DROP COLUMN `github_url`",
    ]
);
