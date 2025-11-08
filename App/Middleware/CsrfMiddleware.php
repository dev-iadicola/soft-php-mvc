<?php

namespace App\Middleware;

use App\Core\Helpers\Log;
use App\Core\Http\Request;
use App\Core\Services\CsrfService;
use App\Core\Contract\MiddlewareInterface;

class CsrfMiddleware implements MiddlewareInterface
{   
    public function exec(Request $request)
    {
        if(in_array($request->getRequestMethod(), ['POST','PUT','Sessione Avviata'])){
            $csfr = new CsrfService();
            $sessToken = $csfr->getToken();
            $reqTok =  $request->_token  ?? null;
            
            if(! $sessToken || !$reqTok || ! hash_equals($sessToken, $reqTok)){
               Log::alert("Invalid CSRF: token in session = $sessToken, token in request = $reqTok");
                return response()->set419();
                
            }
        }
    }
}