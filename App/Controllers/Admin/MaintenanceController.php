<?php

namespace App\Controllers\Admin;


use App\Core\Config;
use App\Core\Controllers\AdminController;
use App\Core\Controllers\AuthenticationController;
use App\Core\Http\Request;
use App\Core\Http\Attributes\RouteAttr;

class MaintenanceController extends AdminController
{


    #[RouteAttr(path: 'set', method: 'get', name: 'set')]
    public function index()
    {
        $env = getenv('MAINTENANCE');
        return $this->render('admin.settings', [], compact('env'));
    }

    #[RouteAttr(path: 'set', method: 'POST', name: 'set.submit')]
    public function submit(Request $request)
    {
        // Prendo la rotta dell 'env
        $root = $this->mvc->config->folder->root . '\.env';
        if (isset($ $request->check)) {
            $valueForEnv = 'true';
            Config::updateEnv($root, 'MAINTENANCE', $valueForEnv);
            return response()->back()->withSuccess('Manutenzione attivata');
        } else {
            $valueForEnv = 'FALSE';
            Config::updateEnv($root, 'MAINTENANCE', $valueForEnv);
            return response()->back()->withSuccess('SITO WEB ATTIVATO');
        }
    }
}
