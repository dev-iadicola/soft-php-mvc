<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Attributes\Post;
use App\Services\TerminalService;

#[Prefix('/admin')]
#[Middleware('auth')]
class TerminalController extends AdminController
{
    #[Get('/terminal', 'admin.terminal')]
    public function index(): void
    {
        $commands = TerminalService::getAllowedCommands();
        view('admin.terminal', compact('commands'));
    }

    #[Post('/terminal', 'admin.terminal.run')]
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
