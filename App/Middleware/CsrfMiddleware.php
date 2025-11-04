<?php

namespace App\Middleware;

use App\Core\Http\Request;
use App\Core\Contract\MiddlewareInterface;
use App\Core\Facade\Session;

class CsrfMiddleware implements MiddlewareInterface
{
    public function exec(Request $request)
    {
        if(in_array($request->getRequestMethod(), ['POST','PUT','DELETE'])){
            $token = Session::get('csrf_token');
            $incoming =  $request->_token  ?? null;
            
            if(! $token || !$incoming || is_null($token) || is_null($incoming) || hash_equals($token, $incoming)){
               
                return response()->set419();
                
            }
        }
    }
}