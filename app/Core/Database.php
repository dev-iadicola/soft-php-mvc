<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Summary of namespace App\Core
 * 
 * Classe per la connessione al database.
 * 
 */

use PDO;

class Database
{

    private PDO $pdo;
    private static ?Database $instance = null;

    private function __construct()
    {

        // definiamo le credenziali
        $host = GetEnv::requiredString('DB_HOST');
        $user = GetEnv::requiredString('DB_USER');
        $psw = GetEnv::requiredString('DB_PSW');
        $name = GetEnv::requiredString('DB_NAME');
        $port = GetEnv::int('DB_PORT', 3306) ?? 3306;


        $_DSN = "mysql:host={$host};port={$port};dbname={$name};charset=utf8";


        $this->pdo = new PDO($_DSN, $user, $psw, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false // maggior sicurezza attacchi SQL injection
        ]);
    }

    public static function getInstance(): Database
    {
        // Lazy initialization (istanza creata solo la prima volta)
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Restituisce l’oggetto PDO per eseguire query
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

      // Impedisce la clonazione o unserializzazione
      private function __clone(): void {}
      public function __wakeup(): void
      {
          throw new \Exception("invalid argument");
      }
}
