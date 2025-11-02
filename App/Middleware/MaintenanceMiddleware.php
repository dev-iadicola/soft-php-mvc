<?php

namespace App\Middleware;

use App\Core\Mvc;
use App\Core\Contract\MiddlewareInterface;
use App\Core\Http\Request;

class MaintenanceMiddleware implements MiddlewareInterface
{
    public function exec(Request $request)
    {

        $stringManitence = strtoupper(getenv('MAINTENANCE')); // Prendiamo lo status di manutenzione
       
        $actualPath = mvc()->request->uri(); // recuperiamo il percorso URL dell'utente che naviga
     
        
        if ($stringManitence === 'TRUE' && $actualPath !== '/coming-soon' && $actualPath !== '/login'){
           return mvc()->response->redirect('/coming-soon');
        }
    }
}
