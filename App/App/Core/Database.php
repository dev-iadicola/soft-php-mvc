<?php
namespace App\Core;

/**
 * Summary of namespace App\Core
 * 
 * Classe per la connessione al database.
 * 
 */
use PDO;

class Database {

    public PDO $pdo;

    public function __construct() {

        // definiamo le credenziali
        define('HOST', getenv('DB_HOST'));
        define('USER', getenv('DB_USER'));
        define('PSW', getenv('DB_PSW'));
        define('NAME', getenv('DB_NAME'));
        define('PORT', getenv('DB_PORT') ?: 3306); // Usa la porta 3306 come predefinita se DB_PORT non è definito

        $_DSN =  "mysql:host=".HOST.";
        port=".PORT.";
        dbname=".NAME.";
        charset=utf8";

        $this->pdo = new PDO($_DSN, USER, PSW, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false // maggior sicurezza attacchi SQL injection
        ]);

    }

}