<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\Config;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\DataLayer\Seeder\SeederRunner;

class SeedCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        Config::env(getcwd() . '/.env');

        $seederPath = getcwd() . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'seed';
        $runner = new SeederRunner($seederPath);
        $count = $runner->runSeed();

        if ($count > 0) {
            Out::success("$count seeder(s) executed.");
        }
    }
}
