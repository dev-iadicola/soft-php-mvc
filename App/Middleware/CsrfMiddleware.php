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
        if(in_array($request->getRequestMethod(), ['POST','PUT','DELETE'])){
            $csfr = new CsrfService();
            $token = $csfr->getToken();
            $incoming =  $request->_token  ?? null;
            
            if(! $token || !$incoming || !hash_equals($token, $incoming)){
               Log::alert("Invalid CSRF: token in session = $token, token in request = $incoming");
                return response()->set419();
                
            }
        }
    }
}