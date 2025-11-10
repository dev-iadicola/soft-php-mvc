<?php

namespace App\Core\Provider;

use PDOException;
use App\Core\Database;
use App\Core\Helpers\Log;
use App\Core\Http\Response;
use App\Utils\Enviroment;

class DatabaseProvider
{
    public function __construct(private Response $response) {}
    public function register()
    {
        try {
            return Database::getInstance()->getConnection();
        } catch (PDOException $e) {
            if (!Enviroment::isDebug()) {
                $this->response->set500()->send();
              
                Log::alert(
                    "! CONNESSIONE DATABASE ASSENTE, VERIFICA IL MOTIVO. ! \n
                    {$e->getMessage()} \n
                    at file {$e->getFile()} \n
                    at line {$e->getLine()}"
                    );
            } else {
                $this->response->set500("Database connection error " . $e->getMessage())->send();
            }

            exit;
        }
    }
}
