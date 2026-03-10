<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;

class TestCommand implements CommandInterface
{
    private string $phpBin = 'php';
    private string $phpunit = 'vendor/bin/phpunit';
    private string $config = 'phpunit.xml';

    public function exe(array $command): void
    {
        $root = getcwd();
        $phpunit = $root . '/' . $this->phpunit;

        if (!file_exists($phpunit)) {
            Out::error("PHPUnit not found at {$phpunit}. Run: composer require --dev phpunit/phpunit");
            return;
        }

        if (!file_exists($root . '/' . $this->config)) {
            Out::error("PHPUnit config not found: {$this->config}");
            return;
        }

        $args = $this->parseArgs(array_slice($command, 2));
        $cmd = $this->buildCommand($phpunit, $args);

        Out::ln("Running: {$cmd}\n");

        passthru($cmd, $exitCode);

        echo "\n";
        if ($exitCode === 0) {
            Out::ok("All tests passed!");
        } else {
            Out::error("Tests failed with exit code {$exitCode}");
        }
    }

    private function parseArgs(array $rawArgs): array
    {
        $args = [
            'path'     => null,
            'filter'   => null,
            'compact'  => false,
            'coverage' => false,
            'suite'    => null,
            'group'    => null,
            'stop'     => false,
            'extra'    => [],
        ];

        for ($i = 0; $i < count($rawArgs); $i++) {
            $arg = $rawArgs[$i];

            switch ($arg) {
                case '--compact':
                    $args['compact'] = true;
                    break;

                case '--filter':
                case '-f':
                    $args['filter'] = $rawArgs[++$i] ?? null;
                    break;

                case '--suite':
                case '-s':
                    $args['suite'] = $rawArgs[++$i] ?? null;
                    break;

                case '--group':
                case '-g':
                    $args['group'] = $rawArgs[++$i] ?? null;
                    break;

                case '--coverage':
                    $args['coverage'] = true;
                    break;

                case '--stop':
                    $args['stop'] = true;
                    break;

                case '--help':
                    $this->printHelp();
                    exit(0);

                default:
                    // If it doesn't start with -- it's a path/file
                    if (!str_starts_with($arg, '--')) {
                        $args['path'] = $arg;
                    } else {
                        $args['extra'][] = $arg;
                    }
                    break;
            }
        }

        return $args;
    }

    private function buildCommand(string $phpunit, array $args): string
    {
        $parts = [$this->phpBin, $phpunit];

        // Test path or default
        $parts[] = escapeshellarg($args['path'] ?? 'tests/');

        // Testdox (verbose) is default, --compact disables it
        if (!$args['compact']) {
            $parts[] = '--testdox';
        }

        if ($args['filter']) {
            $parts[] = '--filter';
            $parts[] = escapeshellarg($args['filter']);
        }

        if ($args['suite']) {
            $parts[] = '--testsuite';
            $parts[] = escapeshellarg($args['suite']);
        }

        if ($args['group']) {
            $parts[] = '--group';
            $parts[] = escapeshellarg($args['group']);
        }

        if ($args['coverage']) {
            $parts[] = '--coverage-text';
        }

        if ($args['stop']) {
            $parts[] = '--stop-on-failure';
        }

        $parts[] = '--colors=always';

        foreach ($args['extra'] as $extra) {
            $parts[] = escapeshellarg($extra);
        }

        return implode(' ', $parts);
    }

    private function printHelp(): void
    {
        Out::ln("Usage: php soft test [path] [options]\n");
        Out::ln("Arguments:");
        Out::ln("  [path]              Test file or directory (default: tests/)");
        Out::ln("");
        Out::ln("Options:");
        Out::ln("  --compact           Compact output (no testdox)");
        Out::ln("  --filter, -f <name> Filter tests by name");
        Out::ln("  --suite, -s <name>  Run a specific test suite");
        Out::ln("  --group, -g <name>  Run tests in a specific group");
        Out::ln("  --coverage          Show code coverage (requires Xdebug/PCOV)");
        Out::ln("  --stop              Stop on first failure");
        Out::ln("  --help              Show this help message");
        Out::ln("");
        Out::ln("Examples:");
        Out::ln("  php soft test                              Run all tests");
        Out::ln("  php soft test --compact                    Compact output");
        Out::ln("  php soft test tests/Unit/Model/            Run Model tests only");
        Out::ln("  php soft test --filter testPlural          Run matching tests");
        Out::ln("  php soft test --stop --compact             Stop on first failure");
    }
}
