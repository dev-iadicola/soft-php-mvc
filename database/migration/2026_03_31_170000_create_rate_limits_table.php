<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::rawSql(
    [
        <<<SQL
        CREATE TABLE `rate_limits` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `ip` VARCHAR(45) NOT NULL,
            `route` VARCHAR(255) NOT NULL,
            `attempts` INT NOT NULL DEFAULT 0,
            `last_attempt_at` DATETIME NOT NULL,
            UNIQUE KEY `rate_limits_ip_route_unique` (`ip`, `route`)
        )
        SQL,
    ],
    [
        'DROP TABLE `rate_limits`',
    ]
);
