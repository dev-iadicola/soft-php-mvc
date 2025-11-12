<?php

namespace App\Core\Eloquent\Query;

use PDO;
use PDOException;
use PDOStatement;
use App\Core\Helpers\Log;
use App\Core\Exception\QueryBuilderException;

class QueryExecutor
{
    public function __construct(private PDO $pdo)
    {

    }

    public function prepare($query){
        return $this->pdo->prepare($query);
    }

    //───────────────────────────────────────────────────────────────
    //* EXECUTION QUERY 
    //───────────────────────────────────────────────────────────────
    #region STATMENT - EXECUTION - FETCH

    public function prepareAndExecute(string $query, array $bindings): PDOStatement
    {
 
        try {
            $stmt = $this->pdo->prepare($query);
            foreach ($bindings as $bind => $value) {
                $stmt->bindParam($bind, $value);
            }
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            Log::debug("Wrong query HERE -> $query");
            throw new QueryBuilderException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function fetch(string $query, array $bindings,  int $fetchTyep = PDO::FETCH_ASSOC): array|object|bool
    {
        $stmt = $this->prepareAndExecute(query: $query, bindings: $bindings );
        if (!$stmt instanceof PDOStatement) {
            return false; // errore o query non eseguita
        }
        return $stmt->fetch($fetchTyep);
    }

    public function fetchAll(string $query, array $bindings, int $fetchType = PDO::FETCH_ASSOC): array|object|bool
    {
        $stmt = $this->prepareAndExecute($query, $bindings);
        if (!$stmt instanceof PDOStatement) {
            return false; // errore o query non eseguita
        }
        return $stmt->fetchAll($fetchType);
    }

    public function fetchColumn(?string $query, array $bindings): bool|int
    {
        $stmt = $this->prepareAndExecute($query, $bindings);
        if (!$stmt instanceof PDOStatement) {
            return false; // errore o query non eseguita
        }
        return (int) $stmt->fetchColumn();

    }

    public function lastInsertId(): bool|string{
        return $this->pdo->lastInsertId();
    }
}
