<?php

namespace App\Core\CLI\Commands;

use App\Core\Config;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\DataLayer\Migration\Migrator;

class MigrateStatusCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        Config::env(getcwd() . '/.env');

        $migrationPath = getcwd() . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'migration';
        $migrator = new Migrator($migrationPath);

        $status = $migrator->status();

        if (empty($status)) {
            Out::ln('No migrations found.');
            return;
        }

        Out::ln(str_pad('Migration', 50) . str_pad('Batch', 10) . 'Status');
        Out::ln(str_repeat('-', 70));

        foreach ($status as $row) {
            $batch = $row['batch'] !== null ? (string) $row['batch'] : '-';
            $line = str_pad($row['migration'], 50)
                  . str_pad($batch, 10)
                  . $row['status'];
            Out::ln($line);
        }
    }
}
