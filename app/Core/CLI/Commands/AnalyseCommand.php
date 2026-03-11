<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\Helpers\Log;

class AnalyseCommand implements CommandInterface
{
    private string $phpBin = 'php';

    private string $phpstan = 'vendor/bin/phpstan';

    public function exe(array $command): void
    {
        try {
            $root = getcwd();
            $phpstan = $root . '/' . $this->phpstan;

            if (! file_exists($phpstan)) {
                throw new \RuntimeException("PHPStan not found at {$phpstan}. Run: composer require --dev phpstan/phpstan");
            }

            $args = array_slice($command, 2);
            $cmd = $this->buildCommand($phpstan, $args);

            Out::ln("Running: {$cmd}\n");

            passthru($cmd, $exitCode);

            echo "\n";
            if ($exitCode === 0) {
                Out::ok('PHPStan analysis passed!');
            } else {
                throw new \RuntimeException("PHPStan found errors (exit code {$exitCode})");
            }
        } catch (\Throwable $e) {
            Log::exception($e);
            Out::error($e->getMessage());
        }
    }

    private function buildCommand(string $phpstan, array $args): string
    {
        $parts = [$this->phpBin, $phpstan, 'analyse'];

        foreach ($args as $arg) {
            match ($arg) {
                '--baseline' => $parts[] = '--generate-baseline',
                '--help' => $this->printHelp() || exit(0),
                default => $parts[] = escapeshellarg($arg),
            };
        }

        return implode(' ', $parts);
    }

    private function printHelp(): bool
    {
        Out::ln("Usage: php soft analyse [options]\n");
        Out::ln('Options:');
        Out::ln('  --baseline          Generate baseline file for existing errors');
        Out::ln('  --help              Show this help message');
        Out::ln('');
        Out::ln('Examples:');
        Out::ln('  php soft analyse               Run PHPStan analysis');
        Out::ln('  php soft analyse --baseline     Generate baseline');

        return true;
    }
}
