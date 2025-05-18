<?php

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

    public PDO $pdo;

    public function __construct()
    {

        // definiamo le credenziali
        $host = getenv('DB_HOST');
        $user = getenv('DB_USER');
        $psw = getenv('DB_PSW');
        $name = getenv('DB_NAME');
        $port = getenv('DB_PORT') ?: 3306; // Usa la porta 3306 come predefinita se DB_PORT non è definito


        $_DSN = "mysql:host={$host};port={$port};dbname={$name};charset=utf8";


        $this->pdo = new PDO($_DSN, $user, $psw, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false // maggior sicurezza attacchi SQL injection
        ]);
    }
}
