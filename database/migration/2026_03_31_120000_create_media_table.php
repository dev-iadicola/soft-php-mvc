<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::rawSql(
    ["CREATE TABLE `media` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `entity_type` VARCHAR(50) NOT NULL,
        `entity_id` INT UNSIGNED NOT NULL,
        `path` VARCHAR(500) NOT NULL,
        `disk` VARCHAR(50) NOT NULL DEFAULT 'public',
        `sort_order` INT NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_media_entity` (`entity_type`, `entity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"],
    ["DROP TABLE IF EXISTS `media`"]
);
