<?php

declare(strict_types=1);

namespace App\Controllers\Admin;


use App\Core\GetEnv;
use App\Services\MaintenanceService;
use App\Core\Controllers\AdminController;
use App\Core\Http\Request;
use App\Core\Http\Attributes\RouteAttr;

class MaintenanceController extends AdminController
{


    #[RouteAttr(path: 'settings', method: 'get', name: 'admin.settings')]
    public function index()
    {
        $env = GetEnv::bool('MAINTENANCE', false);
        return $this->render('admin.settings', compact('env'));
    }

    #[RouteAttr(path: 'settings', method: 'POST', name: 'admin.settings.submit')]
    public function submit(Request $request)
    {
        $root = $this->mvc->config->get('folder')->root . '/.env';
        if ($request->has('check')) {
            MaintenanceService::enable($root);
            return response()->back()->withSuccess('Manutenzione attivata');
        }

        MaintenanceService::disable($root);
        return response()->back()->withSuccess('Sito web attivato');
    }
}
