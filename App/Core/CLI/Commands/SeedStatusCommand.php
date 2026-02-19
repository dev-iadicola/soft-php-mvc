<?php

namespace App\Core\CLI\Commands;

use App\Core\Config;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\DataLayer\Seeder\SeederRunner;

class SeedStatusCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        Config::env(getcwd() . '/.env');

        $seederPath = getcwd() . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'seed';
        $runner = new SeederRunner($seederPath);
        $status = $runner->status();

        if (empty($status)) {
            Out::ln('No seeders found.');
            return;
        }

        Out::ln(str_pad('Seeder', 50) . str_pad('Batch', 10) . 'Status');
        Out::ln(str_repeat('-', 70));

        foreach ($status as $row) {
            $batch = $row['batch'] !== null ? (string) $row['batch'] : '-';
            $line = str_pad($row['seeder'], 50) . str_pad($batch, 10) . $row['status'];
            Out::ln($line);
        }
    }
}
