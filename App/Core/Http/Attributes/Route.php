<?php 
namespace App\Core\Http\Attributes;
#[\Attribute(\Attribute::TARGET_METHOD)]
class Route{
    /**
     * Summary of __construct
     * @param string            $path esempio: "user/{id}"
     * @param string            $method  GET|POST|PUT|PATCH|DELETE ...   
     * @param string|null       $name  Nome rotta opzionale (per reverse routing futuro)
     */
    public function __construct(
        public string $path,
        public string $method = 'GET',
        public ?string $name = null, 
    ){}
}