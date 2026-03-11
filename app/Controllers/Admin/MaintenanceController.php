<?php

declare(strict_types=1);

namespace App\Controllers\Admin;


use App\Core\Config;
use App\Core\Controllers\AdminController;
use App\Core\Http\Request;
use App\Core\Http\Attributes\RouteAttr;

class MaintenanceController extends AdminController
{


    #[RouteAttr(path: 'settings', method: 'get', name: 'admin.settings')]
    public function index()
    {
        $env = getenv('MAINTENANCE');
        return $this->render('admin.settings', compact('env'));
    }

    #[RouteAttr(path: 'settings', method: 'POST', name: 'admin.settings.submit')]
    public function submit(Request $request)
    {
        $root = $this->mvc->config->folder->root . '/.env';
        if (isset($request->check)) {
            $valueForEnv = 'true';
            Config::updateEnv($root, 'MAINTENANCE', $valueForEnv);
            return response()->back()->withSuccess('Manutenzione attivata');
        }

        $valueForEnv = 'false';
        Config::updateEnv($root, 'MAINTENANCE', $valueForEnv);
        return response()->back()->withSuccess('Sito web attivato');
    }
}
