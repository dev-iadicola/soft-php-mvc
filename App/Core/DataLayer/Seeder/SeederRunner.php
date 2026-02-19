<?php

namespace App\Core\DataLayer\Seeder;

use App\Core\CLI\System\Out;
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

    public function ensureRepository(): void
    {
        if (!$this->repository->repositoryExists()) {
            $this->repository->createRepository();
            Out::ln('[seeders] table created.');
        }
    }

    public function runSeed(): int
    {
        $this->ensureRepository();

        $files = $this->getSeederFiles();
        $ran = $this->repository->getRan();
        $pending = array_diff($files, $ran);

        if (empty($pending)) {
            Out::ln('Nothing to seed.');
            return 0;
        }

        $batch = $this->repository->getLastBatchNumber() + 1;
        $count = 0;

        foreach ($pending as $file) {
            $seeder = $this->resolve($file);

            if (!$seeder instanceof Seeder) {
                Out::warn("Skipping $file: does not return a Seeder instance.");
                continue;
            }

            try {
                $inserted = $seeder->execute();
                $this->repository->log($file, $batch);
                Out::ln("Seeded: $file ($inserted rows)");
                $count++;
            } catch (PDOException $e) {
                Out::error("Failed to seed $file: " . $e->getMessage());
            }
        }

        return $count;
    }

    public function runRollback(): int
    {
        $this->ensureRepository();

        $lastBatch = $this->repository->getLastBatch();

        if (empty($lastBatch)) {
            Out::ln('Nothing to rollback.');
            return 0;
        }

        $count = 0;

        foreach ($lastBatch as $file) {
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
