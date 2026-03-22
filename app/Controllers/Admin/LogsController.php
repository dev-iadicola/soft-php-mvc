<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Post;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Request;
use App\Services\LogService;

#[Prefix('/admin')]
#[Middleware('auth')]
class LogsController extends AdminController
{
    #[Get('/logs', 'admin.logs')]
    public function index()
    {
        $filters = [
            'date_from' => $_GET['date_from'] ?? '',
            'date_to'   => $_GET['date_to'] ?? '',
            'user_id'   => $_GET['user_id'] ?? '',
            'device'    => $_GET['device'] ?? '',
        ];

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $paginator = LogService::getPaginated($filters, $page);
        $devices = LogService::getDistinctDevices();

        return view('admin.logs', compact('paginator', 'filters', 'devices'));
    }

    #[Post('/logs/delete-old', 'admin.logs.deleteOld')]
    public function deleteOld(Request $request)
    {
        $date = $request->string('delete_before');

        if ($date === '') {
            return response()->back()->withError('Seleziona una data valida.');
        }

        $count = LogService::deleteOlderThan($date);

        return response()->back()->withSuccess("Eliminati {$count} log precedenti al {$date}.");
    }

    #[Get('/logs/export', 'admin.logs.export')]
    public function export()
    {
        $filters = [
            'date_from' => $_GET['date_from'] ?? '',
            'date_to'   => $_GET['date_to'] ?? '',
            'user_id'   => $_GET['user_id'] ?? '',
            'device'    => $_GET['device'] ?? '',
        ];

        $csv = LogService::exportCsv($filters);
        $filename = 'logs_export_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($csv));

        echo $csv;
        exit;
    }
}
