<?php

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Model\LogTrace;
use App\Core\Http\Attributes\RouteAttr;

class LogsController extends AdminController
{

    #[RouteAttr(path: '/logs', method: 'get', name: 'admin.logs')]
    public function index()
    {
        $query = "SELECT indirizzo, device, COUNT(*) AS login_count, MAX(last_log) AS last_log
        FROM logs
        GROUP BY indirizzo, device;";

        $logs = LogTrace::query()->query($query);

        return view('admin.logs', compact('logs'));
    }
}
