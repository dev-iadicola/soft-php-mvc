<?php

namespace App\Core\DataLayer\Runtime;

use PDO;
/**
 * ORM Runtime Registry
 *
 * Stores global runtime dependencies for the ORM:
 * - PDO connection
 * - SQL driver name (mysql / postgres)
 *
 * This class is the single source of truth for all ORM components.
 */
class ORM
{
    private static PDO $pdo;

    private static string $driver;

    public static function init(Pdo $pdo, string $drive){
        self::$pdo = $pdo;
        self::$driver = $drive;
    }

    public static function getPDO(): PDO{
        return self::$pdo;
    }

    public static function getDrive():string{
        return self::$driver;
    }

}
