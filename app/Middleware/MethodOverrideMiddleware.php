<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Http\Request;
use App\Core\Contract\MiddlewareInterface;

class MethodOverrideMiddleware implements MiddlewareInterface{
    public function exec(Request $request): mixed {

        if($request->has('_method')){
            // allowed valid method
            if(!in_array(strtoupper($request->string('_method')), ['PUT', 'PATCH','DELETE'])){
                
                return response()->set405();

            }
        }

        return null;
    }
}
