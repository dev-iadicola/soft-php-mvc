<?php

namespace App\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Controllers\AdminController;

class TerminalController extends AdminController
{
    private const ALLOWED_COMMANDS = [
        'seed',
        'migrate',
        'migrate:status',
        'clear:cache',
    ];

    #[RouteAttr('/terminal', 'GET', 'admin.terminal')]
    public function index(): void
    {
        $commands = self::ALLOWED_COMMANDS;
        view('admin.terminal', compact('commands'));
    }

    #[RouteAttr('/terminal', 'POST', 'admin.terminal.run')]
    public function run(Request $request): void
    {
        $commands = self::ALLOWED_COMMANDS;
        $input = trim($request->command ?? '');
        $output = '';
        $error = '';

        if ($input === '') {
            $error = 'Nessun comando inserito.';
            view('admin.terminal', compact('commands', 'input', 'output', 'error'));
            return;
        }

        // Parse: accept both "php soft migrate" and just "migrate"
        $parts = preg_split('/\s+/', $input);
        if ($parts[0] === 'php' && ($parts[1] ?? '') === 'soft') {
            array_shift($parts); // remove "php"
            array_shift($parts); // remove "soft"
        }

        $command = $parts[0] ?? '';

        if (!in_array($command, self::ALLOWED_COMMANDS, true)) {
            $error = "Comando non consentito: \"$command\". Comandi disponibili: " . implode(', ', self::ALLOWED_COMMANDS);
            view('admin.terminal', compact('commands', 'input', 'output', 'error'));
            return;
        }

        $softPath = baseRoot() . '/soft';
        $args = array_map('escapeshellarg', array_slice($parts, 1));
        $fullCommand = 'php ' . escapeshellarg($softPath) . ' ' . escapeshellarg($command) . ' ' . implode(' ', $args) . ' 2>&1';

        $output = shell_exec($fullCommand) ?? '';

        view('admin.terminal', compact('commands', 'input', 'output', 'error'));
    }
}
