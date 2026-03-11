<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Controllers\AdminController;
use App\Services\TerminalService;

class TerminalController extends AdminController
{
    #[RouteAttr('/terminal', 'GET', 'admin.terminal')]
    public function index(): void
    {
        $commands = TerminalService::getAllowedCommands();
        view('admin.terminal', compact('commands'));
    }

    #[RouteAttr('/terminal', 'POST', 'admin.terminal.run')]
    public function run(Request $request): void
    {
        $commands = TerminalService::getAllowedCommands();
        $input = trim($request->string('command'));

        $result = TerminalService::execute($input);
        $output = $result['output'];
        $error = $result['error'];

        view('admin.terminal', compact('commands', 'input', 'output', 'error'));
    }
}
