<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::rawSql(
    [
        <<<SQL
        CREATE TABLE `sessions` (
            `id` VARCHAR(128) NOT NULL PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `ip` VARCHAR(45) NOT NULL,
            `user_agent` VARCHAR(512) NULL,
            `last_activity` DATETIME NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            KEY `sessions_user_id_index` (`user_id`)
        )
        SQL,
    ],
    [
        'DROP TABLE `sessions`',
    ]
);
