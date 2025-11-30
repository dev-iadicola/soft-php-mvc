<?php

namespace App\Core\Http\Attributes;

use App\Core\Contract\MiddlewareInterface;

#[\Attribute(\Attribute::TARGET_METHOD)]
class RouteAttr
{
    /**
     * Summary of __construct
     * @param string            $path esempio: "user/{id}"
     * @param string            $method  GET|POST|PUT|PATCH|DELETE ...   
     * @param string|array<string>|null     $middleware  nomi liste middleware (es: 'auth' o ['auth','admin']) 
     * @param string|null       $name  Nome rotta opzionale (per reverse routing futuro)
     */
    public function __construct(
        public string $path,
        public string $method = 'GET',
        public ?string $name = null,
        public array|string|null $middleware = null,

    ) {

       

        /**
         *  Normalizza il path
         * - Se contiene punti (.), li converte in slash
         * - Se non inizia con uno slash, lo aggiunge
         */
        $this->path = str_replace('.', '/', trim($this->path));
        if (!str_starts_with($this->path, '/')) {
            $this->path = '/' . $this->path;
        }
        // Normalizza sempre in array
        $middlewares = is_array($this->middleware)
            ? $this->middleware
            : ($this->middleware ? [$this->middleware] : []);

        // Aggiungi sempre 'web' se non presente
        if (!in_array('web', $middlewares, true)) {
            array_unshift($middlewares, 'web');
        }

        $this->middleware = $middlewares;
    }
}
