<?php

namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Core\Config;
use App\Core\Controller;
use App\Core\Http\Request;

class MaintenanceController extends AbstractAdminController
{


    public function index()
    {
        $env = getenv('MAINTENANCE');
        return $this->render('admin.settings', [], compact('env'));
    }

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
