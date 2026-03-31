<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::rawSql(
    ["CREATE TABLE `email_templates` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `slug` VARCHAR(100) NOT NULL UNIQUE,
        `subject` VARCHAR(255) NOT NULL,
        `body` TEXT NOT NULL,
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"],
    ["DROP TABLE IF EXISTS `email_templates`"]
);
