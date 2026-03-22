<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\Config;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\DataLayer\Migration\Migrator;
use App\Core\DataLayer\Seeder\SeederRunner;

class MigrateFreshCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        Config::env(getcwd() . '/.env');

        $migrationPath = getcwd() . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migration';
        $migrator = new Migrator($migrationPath);

        $count = $migrator->fresh();

        if ($count > 0) {
            Out::success("$count migration(s) executed.");
        }

        if (\in_array('--seed', $command, true)) {
            $runner = new SeederRunner(SeederRunner::defaultPath());
            $seeded = $runner->runSeed();

            if ($seeded > 0) {
                Out::success("$seeded seeder(s) executed.");
            }
        }
    }
}
