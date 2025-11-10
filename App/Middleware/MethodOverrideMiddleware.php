<?php 
namespace App\Middleware;

use App\Core\Http\Request;
use App\Core\Contract\MiddlewareInterface;

class MethodOverrideMiddleware implements MiddlewareInterface{
    public function exec(Request $request){

        if(isset($request->_method)){
            // allowed valid method
            if(!in_array(strtoupper($request->_method), ['PUT', 'PATCH','DELETE'])){
                
                return response()->set405();
                
            }
        }
    }
}