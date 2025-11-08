<?php

namespace App\Controllers\Admin;

use App\Core\Controllers\AuthenticationController;
use App\Model\LogTrace;
use App\Core\Http\Attributes\RouteAttr;

class LogsController extends AuthenticationController
{

    #[RouteAttr(path: '/log', method: 'get', name: 'logs')]
    public function index()
    {
        $query = "SELECT indirizzo, device, COUNT(*) AS login_count, MAX(last_log) AS last_log
        FROM logs
        GROUP BY indirizzo, device;";

        $logs = LogTrace::query($query);

        // Utilizza la funzione `view` per passare direttamente le variabili alla vista
        return view('admin.logs',compact('logs') );
    }
}
