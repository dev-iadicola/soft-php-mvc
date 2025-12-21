<?php

declare(strict_types=1);

namespace App\Core\DataLayer\Query;

use PDO;
use PDOException;
use PDOStatement;

class QueryExecutor
{
    public function __construct(private PDO $pdo) {}

    public function prepare($query)
    {
        return $this->pdo->prepare($query);
    }

    // ───────────────────────────────────────────────────────────────
    // * EXECUTION QUERY
    // ───────────────────────────────────────────────────────────────
    // region STATMENT - EXECUTION - FETCH

    public function prepareAndExecute(string $query, array $bindings): PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($query);
            foreach ($bindings as $bind => $value) {
                $stmt->bindValue($bind, $value);
            }
            $stmt->execute();

            return $stmt;
        } catch (PDOException $e) {
            preg_match('/\bFROM\s+([`"\w]+)/i', $query, $mFrom);
            preg_match('/\bINTO\s+([`"\w]+)/i', $query, $mInto);

            $table = $mFrom[1] ?? $mInto[1] ?? 'unknown_table';

            throw new PDOException(
                $e->getMessage() . " [table: {$table}]",
                (int) $e->getCode()
            );
        }
    }

    public function fetch(string $query, array $bindings, int $fetchType = PDO::FETCH_ASSOC): array|object|bool
    {
        try {

            $stmt = $this->prepareAndExecute(query: $query, bindings: $bindings);
            if ( ! $stmt instanceof PDOStatement) {
                return false; // errore o query non eseguita
            }

            return $stmt->fetch($fetchType);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), code: (int) $e->getCode());
        }

    }

    public function fetchAll(string $query, array $bindings, int $fetchType = PDO::FETCH_ASSOC): array|object|bool
    {
        $stmt = $this->prepareAndExecute($query, $bindings);
        if ( ! $stmt instanceof PDOStatement) {
            return false; // errore o query non eseguita
        }

        return $stmt->fetchAll($fetchType);
    }

    public function fetchColumn(?string $query, array $bindings): bool|int
    {
        $stmt = $this->prepareAndExecute($query, $bindings);
        if ( ! $stmt instanceof PDOStatement) {
            return false; // errore o query non eseguita
        }

        return (int) $stmt->fetchColumn();

    }

    public function lastInsertId(): bool|string
    {
        return $this->pdo->lastInsertId();
    }
}
