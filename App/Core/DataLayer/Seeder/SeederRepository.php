<?php

namespace App\Core\DataLayer\Seeder;

use App\Core\Database;
use PDO;

class SeederRepository
{
    private PDO $pdo;
    private string $table = 'seeders';

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function createRepository(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `seeder` VARCHAR(255) NOT NULL,
            `batch` INT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

        $this->pdo->exec($sql);
    }

    public function repositoryExists(): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?"
        );
        $stmt->execute([$this->table]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function getRan(): array
    {
        $stmt = $this->pdo->query(
            "SELECT `seeder` FROM `{$this->table}` ORDER BY `batch`, `seeder`"
        );
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getLastBatchNumber(): int
    {
        $stmt = $this->pdo->query(
            "SELECT MAX(`batch`) FROM `{$this->table}`"
        );
        return (int) $stmt->fetchColumn();
    }

    public function getLastBatch(): array
    {
        $lastBatch = $this->getLastBatchNumber();
        if ($lastBatch === 0) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            "SELECT `seeder` FROM `{$this->table}` WHERE `batch` = ? ORDER BY `seeder` DESC"
        );
        $stmt->execute([$lastBatch]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function log(string $seeder, int $batch): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO `{$this->table}` (`seeder`, `batch`) VALUES (?, ?)"
        );
        $stmt->execute([$seeder, $batch]);
    }

    public function delete(string $seeder): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM `{$this->table}` WHERE `seeder` = ?"
        );
        $stmt->execute([$seeder]);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT `seeder`, `batch`, `created_at` FROM `{$this->table}` ORDER BY `batch`, `seeder`"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
