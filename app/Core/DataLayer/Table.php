<?php

declare(strict_types=1);

namespace App\Core\DataLayer;

use App\Core\Database;
use PDO;

/**
 * Lightweight query runner for raw/aggregate queries.
 *
 * Returns plain arrays instead of hydrated Models.
 * Use this when you need GROUP BY, COUNT, SUM, JOIN
 * or any query that doesn't map 1:1 to a Model.
 *
 * Usage:
 *   $table = new Table('visitors');
 *   $total = $table->scalar('SELECT COUNT(*) FROM visitors');
 *   $rows  = $table->fetchAll('SELECT url, COUNT(*) AS count FROM visitors GROUP BY url');
 *   $row   = $table->fetchOne('SELECT * FROM visitors WHERE id = :id', [':id' => 1]);
 */
class Table
{
    private PDO $pdo;

    public function __construct(private string $table)
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Esegue una query e restituisce tutte le righe come array associativi.
     *
     * @param array<string, mixed> $params
     * @return array<int, array<string, mixed>>
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Esegue una query e restituisce una singola riga.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>|null
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? $row : null;
    }

    /**
     * Esegue una query e restituisce un singolo valore scalare (prima colonna, prima riga).
     *
     * @param array<string, mixed> $params
     */
    public function scalar(string $sql, array $params = []): mixed
    {
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Scorciatoia: COUNT(*) sulla tabella con condizioni opzionali.
     *
     * @param array<string, mixed> $params
     */
    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }

        return (int) $this->scalar($sql, $params);
    }

    /**
     * Scorciatoia: COUNT(DISTINCT column) sulla tabella.
     *
     * @param array<string, mixed> $params
     */
    public function countDistinct(string $column, string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(DISTINCT {$column}) FROM {$this->table}";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }

        return (int) $this->scalar($sql, $params);
    }

    public function getTable(): string
    {
        return $this->table;
    }
}
