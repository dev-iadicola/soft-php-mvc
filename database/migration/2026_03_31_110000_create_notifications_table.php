<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::rawSql(
    ["CREATE TABLE `notifications` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `type` VARCHAR(50) NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `message` VARCHAR(500) NULL DEFAULT NULL,
        `link` VARCHAR(255) NULL DEFAULT NULL,
        `is_read` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"],
    ["DROP TABLE IF EXISTS `notifications`"]
);
