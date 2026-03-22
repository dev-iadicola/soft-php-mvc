<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::rawSql(
    ["ALTER TABLE `technology` ADD COLUMN `icon` VARCHAR(100) NULL DEFAULT NULL AFTER `name`"],
    ["ALTER TABLE `technology` DROP COLUMN `icon`"]
);
