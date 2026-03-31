<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::rawSql(
    [
        "ALTER TABLE `users` ADD COLUMN `two_factor_secret` VARCHAR(255) NULL DEFAULT NULL AFTER `token`",
        "ALTER TABLE `users` ADD COLUMN `two_factor_enabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `two_factor_secret`",
    ],
    [
        "ALTER TABLE `users` DROP COLUMN `two_factor_enabled`",
        "ALTER TABLE `users` DROP COLUMN `two_factor_secret`",
    ]
);
