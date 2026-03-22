<?php

declare(strict_types=1);

namespace App\Services;

class TerminalService
{
    private const ALLOWED_COMMANDS = [
        'seed',
        'migrate',
        'migrate:status',
        'storage',
        'clear:cache',
    ];

    /**
     * Check if a command is in the whitelist.
     *
     * @param string $command The command name (without "php soft" prefix)
     */
    public static function isAllowed(string $command): bool
    {
        return in_array($command, self::ALLOWED_COMMANDS, true);
    }

    /**
     * Parse raw input, stripping the optional "php soft" prefix,
     * validate the command, and execute it.
     *
     * @return array{output: string, error: string}
     */
    public static function execute(string $input): array
    {
        $input = trim($input);

        if ($input === '') {
            return ['output' => '', 'error' => 'Nessun comando inserito.'];
        }

        // Parse: accept both "php soft migrate" and just "migrate"
        $parts = preg_split('/\s+/', $input);
        if ($parts[0] === 'php' && ($parts[1] ?? '') === 'soft') {
            array_shift($parts); // remove "php"
            array_shift($parts); // remove "soft"
        }

        $command = $parts[0] ?? '';

        if (!self::isAllowed($command)) {
            $error = "Comando non consentito: \"$command\". Comandi disponibili: " . implode(', ', self::ALLOWED_COMMANDS);
            return ['output' => '', 'error' => $error];
        }

        $softPath = baseRoot() . '/soft';
        $args = array_map('escapeshellarg', array_slice($parts, 1));
        $fullCommand = 'php ' . escapeshellarg($softPath) . ' ' . escapeshellarg($command) . ' ' . implode(' ', $args) . ' 2>&1';

        $output = shell_exec($fullCommand) ?? '';

        return ['output' => $output, 'error' => ''];
    }

    /**
     * @return list<string>
     */
    public static function getAllowedCommands(): array
    {
        return self::ALLOWED_COMMANDS;
    }
}
