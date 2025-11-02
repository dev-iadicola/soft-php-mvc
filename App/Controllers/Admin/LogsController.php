<?php
namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Core\Eloquent\Model;
use App\Model\LogTrace;
use App\Core\Component;
use App\Core\Controller;
use App\Core\Controllers\BaseController;
use App\Core\Http\Request;
use App\Core\Services\AuthService;

class LogsController extends BaseController{

    public function index() {
        
        $query ="SELECT indirizzo, device, COUNT(*) AS login_count, MAX(last_log) AS last_log
        FROM logs
        GROUP BY indirizzo, device;";

        $logs = LogTrace::query($query);
        
        // Utilizza la funzione `view` per passare direttamente le variabili alla vista
        return $this->render('admin.logs',[] ,compact('logs'));
    }
 

}