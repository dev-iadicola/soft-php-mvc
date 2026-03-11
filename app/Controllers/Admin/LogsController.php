<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\RouteAttr;
use App\Services\LogService;

class LogsController extends AdminController
{

    #[RouteAttr(path: '/logs', method: 'get', name: 'admin.logs')]
    public function index()
    {
        $logs = LogService::getLoginStats();

        return view('admin.logs', compact('logs'));
    }
}
