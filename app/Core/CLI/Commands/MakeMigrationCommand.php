<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\CLI\Stubs\StubGenerator;
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

        $filePath = getcwd() . '/Database/migration/' . $fileName;

        StubGenerator::make('migration')
            ->replace(['{{TABLE}}' => $table])
            ->saveTo($filePath);

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
