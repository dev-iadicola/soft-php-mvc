<?php 
namespace App\Core\Connection;

use App\Core\Helpers\Log;
use PDO;
use PDOException;

class Database {

    public PDO $pdo;

    public function __construct() {
        // Prendo le variabili d'ambiente
        $host    = getenv('DB_HOST');
        $user    = getenv('DB_USER');
        $pass    = getenv('DB_PSW');
        $name    = getenv('DB_NAME');
        $port    = getenv('DB_PORT') ?: 3306;
        $charset = 'utf8mb4';

        // Ricompongo correttamente il DSN in un'unica stringa
        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Rilancio un errore più leggibile
             Log::error( ['exception' => $e]);
            throw new \RuntimeException("Connessione al DB fallita: " . $e->getMessage() );
           
        }
    }

    // Se ti serve esporre la connessione
    public function getPdo(): PDO {
        return $this->pdo;
    }
}
