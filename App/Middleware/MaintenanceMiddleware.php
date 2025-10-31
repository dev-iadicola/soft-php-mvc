<?php

namespace App\Middleware;

use App\Core\Mvc;
use App\Core\Contract\MiddlewareInterface;

class MaintenanceMiddleware implements MiddlewareInterface
{
    public function exec()
    {

        $stringManitence = strtoupper(getenv('MAINTENANCE')); // Prendiamo lo status di manutenzione
       
        $actualPath = mvc()->request->uri(); // recuperiamo il percorso URL dell'utente che naviga
     
        
        if ($stringManitence === 'TRUE' && $actualPath !== '/coming-soon' && $actualPath !== '/login'){
           return mvc()->response->redirect('/coming-soon');
        }
    }
}
