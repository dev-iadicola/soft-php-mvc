<?php
namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Core\Eloquent\Model;
use App\Model\Log;
use App\Core\Component;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Services\AuthService;

class LogsController extends Controller{

    public function __construct(public Mvc $mvc) {
        parent::__construct($mvc);
        
        $this->setLayout('admin');
        
    }

    public function index() {
        $orm = new Model($this->mvc->pdo); 

        $query ="SELECT indirizzo, COUNT(*) AS login_count, MAX(last_log) AS last_log
        FROM logs
        GROUP BY indirizzo;";
        $logs = $orm->query($query);

                
        // Utilizza la funzione `view` per passare direttamente le variabili alla vista
        return $this->render('admin.logs',[] ,compact('logs'));

        
    }
 
    private function getComponent($items) {
        $portfolio = new Component('logsitem');
        $portfolio->setItems($items);
        return $portfolio;
    }



}