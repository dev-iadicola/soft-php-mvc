<?php

namespace App\Core\Eloquent\Query;

use App\Core\Eloquent\QueryBuilder;
use PDO;
use PDOStatement;
use App\Core\Helpers\Log;
use App\Core\Exception\QueryBuilderException;
use PDOException;

trait Execute
{


    //───────────────────────────────────────────────────────────────
    //* ESECUZIONE QUERY E FETCH/FETCHALL 
    //───────────────────────────────────────────────────────────────
    #region STATMENT - EXECUTION - FETCH

    private function prepareAndExecute(?string $query = null): PDOStatement
    {
        // ritorno la generazione della stringa query con i parametri da bindare
        $sql = $query ?? $this->toSql();
        
        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($this->bindings as $bind => $value) {
                $stmt->bindParam($bind, $value);
            }
            $stmt->execute();
          
            return  $stmt;
        } catch (PDOException $e) {
            Log::debug("Wrong query HERE -> $sql");
            throw new QueryBuilderException($e);
        }
        

        // return $stmt->fetch($fetchTyep);
    }

    protected function fetch(int $fetchTyep = PDO::FETCH_ASSOC, ?string $query = null): array|object|bool
    {
        $stmt = $this->prepareAndExecute($query);
        if (!$stmt instanceof PDOStatement) {
            return false; // errore o query non eseguita
        }
        return $stmt->fetch($fetchTyep);
    }

    protected function fetchAll(int $fetchType = PDO::FETCH_ASSOC, ?string $query = null): array|object|bool
    {
        $stmt = $this->prepareAndExecute($query);
        if (!$stmt instanceof PDOStatement) {
            return false; // errore o query non eseguita
        }
        return $stmt->fetchAll($fetchType);
    }
}
