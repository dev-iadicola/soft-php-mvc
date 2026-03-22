<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\Config;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\DataLayer\Seeder\SeederRunner;

/**
 * Comando CLI: php soft seed
 *
 * Opzioni:
 *   --class=name   Esegue solo il seeder specificato (es. --class=users_seeder)
 *   --fresh        Rollback di tutti i seeder + re-seed da zero
 */
class SeedCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        Config::env(getcwd() . '/.env');

        $class = $this->parseOption($command, '--class');
        $fresh = \in_array('--fresh', $command, true);

        $runner = new SeederRunner(SeederRunner::defaultPath());

        $count = $fresh
            ? $runner->runFresh($class)
            : $runner->runSeed($class);

        if ($count > 0) {
            Out::success("$count seeder(s) executed.");
        }
    }

    private function parseOption(array $args, string $option): ?string
    {
        foreach ($args as $i => $arg) {
            if (str_starts_with($arg, $option . '=')) {
                return substr($arg, \strlen($option) + 1);
            }
            if ($arg === $option && isset($args[$i + 1]) && !str_starts_with($args[$i + 1], '--')) {
                return $args[$i + 1];
            }
        }
        return null;
    }
}
