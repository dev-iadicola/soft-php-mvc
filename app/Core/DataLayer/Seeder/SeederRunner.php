<?php

declare(strict_types=1);

namespace App\Core\DataLayer\Seeder;

use App\Core\CLI\System\Out;
use App\Core\Database;
use PDOException;

class SeederRunner
{
    private SeederRepository $repository;
    private string $seederPath;

    public function __construct(string $seederPath)
    {
        $this->seederPath = rtrim($seederPath, DIRECTORY_SEPARATOR);
        $this->repository = new SeederRepository();
    }

    public static function defaultPath(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'seed';
    }

    public function ensureRepository(): void
    {
        if (!$this->repository->repositoryExists()) {
            $this->repository->createRepository();
            Out::ln('[seeders] table created.');
        }
    }

    /**
     * Esegue i seeder pendenti.
     *
     * @param string|null $class Nome file specifico da eseguire (es. "users_seeder")
     */
    public function runSeed(?string $class = null): int
    {
        $this->ensureRepository();

        $files = $this->getSeederFiles();
        $ran = $this->repository->getRan();

        if ($class !== null) {
            $match = $this->findSeederByClass($files, $class);
            if ($match === null) {
                Out::error("Seeder not found: $class");
                return 0;
            }
            if (in_array($match, $ran, true)) {
                Out::warn("Seeder already ran: $match");
                return 0;
            }
            $pending = [$match];
        } else {
            $pending = array_values(array_diff($files, $ran));
        }

        if (empty($pending)) {
            Out::ln('Nothing to seed.');
            return 0;
        }

        $batch = $this->repository->getLastBatchNumber() + 1;
        $pdo = Database::getInstance()->getConnection();

        $pdo->beginTransaction();
        try {
            $count = 0;
            foreach ($pending as $file) {
                $seeder = $this->resolve($file);

                if (!$seeder instanceof Seeder) {
                    Out::warn("Skipping $file: does not return a Seeder instance.");
                    continue;
                }

                $inserted = $seeder->execute();
                $this->repository->log($file, $batch);
                Out::ln("Seeded: $file ($inserted rows)");
                $count++;
            }
            $pdo->commit();
            return $count;
        } catch (PDOException $e) {
            $pdo->rollBack();
            Out::error("Seed batch failed, transaction rolled back: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Rollback all + re-seed.
     */
    public function runFresh(?string $class = null): int
    {
        $this->ensureRepository();

        $ran = $this->repository->getRan();
        if (!empty($ran)) {
            $this->rollbackAll();
        }

        return $this->runSeed($class);
    }

    /**
     * Rollback degli ultimi N batch.
     */
    public function runRollback(int $steps = 1): int
    {
        $this->ensureRepository();

        $batches = $this->repository->getBatchNumbers($steps);

        if (empty($batches)) {
            Out::ln('Nothing to rollback.');
            return 0;
        }

        $count = 0;

        foreach ($batches as $batchNumber) {
            $files = $this->repository->getByBatch($batchNumber);

            foreach ($files as $file) {
                $seeder = $this->resolve($file);

                if (!$seeder instanceof Seeder) {
                    Out::warn("Skipping $file: does not return a Seeder instance.");
                    continue;
                }

                try {
                    $seeder->rollback();
                    $this->repository->delete($file);
                    Out::ln("Rolled back (truncated): $file");
                    $count++;
                } catch (PDOException $e) {
                    Out::error("Failed to rollback $file: " . $e->getMessage());
                }
            }
        }

        return $count;
    }

    public function status(): array
    {
        $this->ensureRepository();

        $files = $this->getSeederFiles();
        $ran = $this->repository->getAll();
        $ranMap = [];

        foreach ($ran as $row) {
            $ranMap[$row['seeder']] = $row;
        }

        $status = [];
        foreach ($files as $file) {
            if (isset($ranMap[$file])) {
                $status[] = [
                    'seeder' => $file,
                    'batch' => $ranMap[$file]['batch'],
                    'status' => 'Ran',
                ];
            } else {
                $status[] = [
                    'seeder' => $file,
                    'batch' => null,
                    'status' => 'Pending',
                ];
            }
        }

        return $status;
    }

    private function rollbackAll(): void
    {
        $ran = array_reverse($this->repository->getRan());

        foreach ($ran as $file) {
            $seeder = $this->resolve($file);

            if (!$seeder instanceof Seeder) {
                continue;
            }

            try {
                $seeder->rollback();
                $this->repository->delete($file);
                Out::ln("Rolled back (truncated): $file");
            } catch (PDOException $e) {
                Out::error("Failed to rollback $file: " . $e->getMessage());
            }
        }
    }

    /**
     * Cerca un file seeder per nome parziale (es. "users_seeder" matcha "2026_..._users_seeder.php").
     */
    private function findSeederByClass(array $files, string $class): ?string
    {
        $class = str_replace('.php', '', $class);

        foreach ($files as $file) {
            if (str_ends_with($file, $class . '.php')) {
                return $file;
            }
        }

        return null;
    }

    private function getSeederFiles(): array
    {
        if (!is_dir($this->seederPath)) {
            return [];
        }

        $files = glob($this->seederPath . DIRECTORY_SEPARATOR . '*.php');
        $names = array_map('basename', $files);
        sort($names);

        return $names;
    }

    private function resolve(string $file): mixed
    {
        $path = $this->seederPath . DIRECTORY_SEPARATOR . $file;

        if (!file_exists($path)) {
            Out::warn("Seeder file not found: $path");
            return null;
        }

        return require $path;
    }
}
