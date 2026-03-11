<?php

declare(strict_types=1);

namespace App\Controllers\Admin;


use App\Core\GetEnv;
use App\Services\MaintenanceService;
use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Attributes\Post;
use App\Core\Http\Request;

#[Prefix('/admin')]
#[Middleware('auth')]
class MaintenanceController extends AdminController
{


    #[Get('settings', 'admin.settings')]
    public function index()
    {
        $env = GetEnv::bool('MAINTENANCE', false);
        return view('admin.settings', compact('env'));
    }

    #[Post('settings', 'admin.settings.submit')]
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
