<?php 
namespace App\Core\Http\Attributes;

use App\Core\Contract\MiddlewareInterface;
use App\Core\Http\Request;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class AttributeMiddleware implements MiddlewareInterface {
    /**
     * @param string|string[] $name Nome/i middelware. Es "auth" o ["auth","admin"]
     */
    public function __construct( public string|array|MiddlewareInterface $names){}
    
    public function exec(Request $request){}
}
