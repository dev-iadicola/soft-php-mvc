<?php

namespace App\Core\CLI\Commands;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;

class MakeMigrationCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        $name = $command[2] ?? null;

        if (!$name) {
            $name = readline('Migration name (e.g. create_users_table): ');
            $name = trim($name);
        }

        if (!$name) {
            Out::error('You must specify a migration name.');
            return;
        }

        $table = $this->guessTableName($name);
        $timestamp = date('Y_m_d_His');
        $fileName = "{$timestamp}_{$name}.php";

        $migrationDir = getcwd() . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'migration';

        if (!is_dir($migrationDir)) {
            mkdir($migrationDir, 0755, true);
        }

        $stubPath = __DIR__ . '/../Stubs/migration.stub';
        $stub = file_get_contents($stubPath);
        $content = str_replace('{{TABLE}}', $table, $stub);

        $filePath = $migrationDir . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($filePath, $content);

        Out::ln("Created migration: $fileName");
    }

    private function guessTableName(string $name): string
    {
        $name = strtolower($name);

        // create_users_table → users
        if (preg_match('/^create_(.+)_table$/', $name, $matches)) {
            return $matches[1];
        }

        // create_users → users
        if (preg_match('/^create_(.+)$/', $name, $matches)) {
            return $matches[1];
        }

        return $name;
    }
}
