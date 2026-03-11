<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\Helpers\Log;

class RectorCommand implements CommandInterface
{
    private string $phpBin = 'php';

    private string $rector = 'vendor/bin/rector';

    public function exe(array $command): void
    {
        try {
            $root = getcwd();
            $rector = $root . '/' . $this->rector;

            if (! file_exists($rector)) {
                throw new \RuntimeException("Rector not found at {$rector}. Run: composer require --dev rector/rector");
            }

            $args = array_slice($command, 2);
            $cmd = $this->buildCommand($rector, $args);

            Out::ln("Running: {$cmd}\n");

            passthru($cmd, $exitCode);

            echo "\n";
            if ($exitCode === 0) {
                Out::ok('Rector completed successfully!');
            } else {
                throw new \RuntimeException("Rector process failed with exit code {$exitCode}");
            }
        } catch (\Throwable $e) {
            Log::exception($e);
            Out::error($e->getMessage());
        }
    }

    private function buildCommand(string $rector, array $args): string
    {
        $parts = [$this->phpBin, $rector, 'process'];

        foreach ($args as $arg) {
            match ($arg) {
                '--dry', '--dry-run' => $parts[] = '--dry-run',
                '--clear' => $parts[] = '--clear-cache',
                '--help' => $this->printHelp() || exit(0),
                default => $parts[] = escapeshellarg($arg),
            };
        }

        return implode(' ', $parts);
    }

    private function printHelp(): bool
    {
        Out::ln("Usage: php soft rector [options]\n");
        Out::ln('Options:');
        Out::ln('  --dry, --dry-run    Preview changes without applying');
        Out::ln('  --clear             Clear Rector cache before running');
        Out::ln('  --help              Show this help message');
        Out::ln('');
        Out::ln('Examples:');
        Out::ln('  php soft rector                Run Rector');
        Out::ln('  php soft rector --dry          Preview changes only');
        Out::ln('  php soft rector --clear        Clear cache and run');

        return true;
    }
}
