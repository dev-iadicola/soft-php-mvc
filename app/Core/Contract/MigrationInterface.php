<?php

declare(strict_types=1);

namespace App\Core\Contract;

use PDO;

interface MigrationInterface {
    public static function up(?PDO $pdo = null): void;

    public function down(?PDO $pdo = null): void;
}
