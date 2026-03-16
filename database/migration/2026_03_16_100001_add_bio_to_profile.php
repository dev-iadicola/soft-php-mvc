<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::rawSql(
    "ALTER TABLE `profile` ADD COLUMN `bio` TEXT NULL",
    "ALTER TABLE `profile` DROP COLUMN `bio`"
);
