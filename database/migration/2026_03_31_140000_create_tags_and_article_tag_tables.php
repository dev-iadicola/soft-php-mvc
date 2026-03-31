<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::rawSql(
    [
        "CREATE TABLE `tags` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `slug` VARCHAR(100) NOT NULL UNIQUE,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NULL DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE `article_tag` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `article_id` INT UNSIGNED NOT NULL,
            `tag_id` INT UNSIGNED NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NULL DEFAULT NULL,
            UNIQUE KEY `uk_article_tag` (`article_id`, `tag_id`),
            INDEX `idx_article_tag_article` (`article_id`),
            INDEX `idx_article_tag_tag` (`tag_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ],
    [
        "DROP TABLE IF EXISTS `article_tag`",
        "DROP TABLE IF EXISTS `tags`",
    ]
);
