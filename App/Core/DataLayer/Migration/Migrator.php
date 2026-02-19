<?php

namespace App\Core\DataLayer\Migration;

use App\Core\CLI\System\Out;
use PDOException;

class Migrator
{
    private MigrationRepository $repository;
    private string $migrationPath;

    public function __construct(string $migrationPath)
    {
        $this->migrationPath = rtrim($migrationPath, DIRECTORY_SEPARATOR);
        $this->repository = new MigrationRepository();
    }

    public function ensureRepository(): void
    {
        if (!$this->repository->repositoryExists()) {
            $this->repository->createRepository();
            Out::ln('[migrations] table created.');
        }
    }

    public function runUp(): int
    {
        $this->ensureRepository();

        $files = $this->getMigrationFiles();
        $ran = $this->repository->getRan();
        $pending = array_diff($files, $ran);

        if (empty($pending)) {
            Out::ln('Nothing to migrate.');
            return 0;
        }

        $batch = $this->repository->getLastBatchNumber() + 1;
        $count = 0;

        foreach ($pending as $file) {
            $migration = $this->resolve($file);

            if (!$migration instanceof Migration) {
                Out::warn("Skipping $file: does not return a Migration instance.");
                continue;
            }

            try {
                $migration->executeUp();
                $this->repository->log($file, $batch);
                Out::ln("Migrated: $file");
                $count++;
            } catch (PDOException $e) {
                Out::error("Failed to migrate $file: " . $e->getMessage());
            }
        }

        return $count;
    }

    public function runDown(): int
    {
        $this->ensureRepository();

        $lastBatch = $this->repository->getLastBatch();

        if (empty($lastBatch)) {
            Out::ln('Nothing to rollback.');
            return 0;
        }

        $count = 0;

        foreach ($lastBatch as $file) {
            $migration = $this->resolve($file);

            if (!$migration instanceof Migration) {
                Out::warn("Skipping $file: does not return a Migration instance.");
                continue;
            }

            try {
                $migration->executeDown();
                $this->repository->delete($file);
                Out::ln("Rolled back: $file");
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

        $files = $this->getMigrationFiles();
        $ran = $this->repository->getAll();
        $ranMap = [];

        foreach ($ran as $row) {
            $ranMap[$row['migration']] = $row;
        }

        $status = [];
        foreach ($files as $file) {
            if (isset($ranMap[$file])) {
                $status[] = [
                    'migration' => $file,
                    'batch' => $ranMap[$file]['batch'],
                    'status' => 'Ran',
                ];
            } else {
                $status[] = [
                    'migration' => $file,
                    'batch' => null,
                    'status' => 'Pending',
                ];
            }
        }

        return $status;
    }

    private function getMigrationFiles(): array
    {
        if (!is_dir($this->migrationPath)) {
            return [];
        }

        $files = glob($this->migrationPath . DIRECTORY_SEPARATOR . '*.php');
        $names = array_map('basename', $files);
        sort($names);

        return $names;
    }

    private function resolve(string $file): mixed
    {
        $path = $this->migrationPath . DIRECTORY_SEPARATOR . $file;

        if (!file_exists($path)) {
            Out::warn("Migration file not found: $path");
            return null;
        }

        return require $path;
    }
}
