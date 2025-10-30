<?php

namespace App\Middleware;

use App\Core\Mvc;
use App\Core\Contract\MiddlewareInterface;

class MaintenanceMiddleware implements MiddlewareInterface
{
    public function exec(?Mvc $mvc = null)
    {
        $stringManitence = getenv('MAINTENANCE'); // Prendiamo lo status di manutenzione
        
        $actualPath = $mvc->request->getRequestPath(); // recuperiamo il percorso URL dell'utente che naviga
        
        if ($stringManitence === 'true' && $actualPath !== '/coming-soon' && $actualPath !== '/login' &&  !str_contains( $actualPath,'/admin')){
           return $mvc->response->redirect('/coming-soon');
        }
    }
}
