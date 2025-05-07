<?php 
namespace App\Core\Contract;

use PDO;

interface MigrationInterface {
    public static function up(?PDO $pdo = null);

    public function down(?PDO $pdo = null);
}