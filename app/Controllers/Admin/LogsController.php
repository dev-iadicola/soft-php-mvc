<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Prefix;
use App\Services\LogService;

#[Prefix('/admin')]
#[Middleware('auth')]
class LogsController extends AdminController
{

    #[Get('/logs', 'admin.logs')]
    public function index()
    {
        $logs = LogService::getLoginStats();

        return view('admin.logs', compact('logs'));
    }
}
