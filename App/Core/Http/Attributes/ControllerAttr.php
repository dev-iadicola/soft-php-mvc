<?php 
namespace App\Core\Http\Attributes;

use App\Core\Contract\MiddlewareInterface;
use App\Core\Http\Request;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ControllerAttr  {
    /**
     * @param string|string[] $name Nome/i middelware. Es "auth" o ["auth","admin"]
     */
    public function __construct( public null|string|array $middlewareNames, public  ?string $basePath = null){}
    
}
