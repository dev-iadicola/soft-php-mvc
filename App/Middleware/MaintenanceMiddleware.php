<?php

namespace App\Middleware;

use App\Core\Mvc;
use App\Core\Helpers\Log;
use App\Utils\Enviroment;
use App\Core\Http\Request;
use App\Core\Contract\MiddlewareInterface;

class MaintenanceMiddleware implements MiddlewareInterface
{
    public function exec(Request $request)
    {       
        $currentPath = mvc()->request->uri(); // recuperiamo il percorso URL dell'utente che naviga
       
        Log::info($currentPath);
        if(!Enviroment::isMaintenance() && $currentPath == "/coming-soon"){
            return response()->redirect("/")->send();
        }
        if (  Enviroment::isMaintenance() && !in_array($currentPath, ['/coming-soon', '/login'], true)){
           return mvc()->response->redirect('/coming-soon');
        }
    }
}
