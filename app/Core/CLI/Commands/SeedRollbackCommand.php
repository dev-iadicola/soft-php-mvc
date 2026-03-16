<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\Config;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\DataLayer\Seeder\SeederRunner;

/**
 * Comando CLI: php soft seed:rollback
 *
 * Opzioni:
 *   --step=N   Numero di batch da rollbackare (default: 1)
 */
class SeedRollbackCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        Config::env(getcwd() . '/.env');

        $stepOption = $this->parseOption($command, '--step');
        $steps = $stepOption !== null ? max(1, (int) $stepOption) : 1;

        $runner = new SeederRunner(SeederRunner::defaultPath());
        $count = $runner->runRollback($steps);

        if ($count > 0) {
            Out::success("$count seeder(s) rolled back.");
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
