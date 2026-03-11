<?php

declare(strict_types=1);

namespace App\Core\Http\Attributes;

/**
 * Classe base per tutti gli attributi di routing Spatie-style.
 *
 * Non va usata direttamente: usare Get, Post, Put, Patch, Delete.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class RouteAttribute
{
    public string $path;
    public string $method;
    public ?string $name;
    /** @var string[] */
    public array $middleware;

    /**
     * @param string              $path       Es: "/users/{id}"
     * @param string              $method     HTTP method (GET, POST, PUT, PATCH, DELETE)
     * @param string|null         $name       Nome rotta opzionale (per reverse routing)
     * @param string[]|string|null $middleware Nomi liste middleware
     */
    public function __construct(
        string $path,
        string $method = 'GET',
        ?string $name = null,
        array|string|null $middleware = null,
    ) {
        // Normalizza il path: punti -> slash, garantisci lo slash iniziale
        $this->path = str_replace('.', '/', trim($path));
        if (!str_starts_with($this->path, '/')) {
            $this->path = '/' . $this->path;
        }

        $this->method = strtoupper($method);
        $this->name = $name;

        // Normalizza middleware in array
        $middlewares = is_array($middleware)
            ? $middleware
            : ($middleware ? [$middleware] : []);

        // Aggiungi sempre 'web' se non presente
        if (!in_array('web', $middlewares, true)) {
            array_unshift($middlewares, 'web');
        }

        $this->middleware = $middlewares;
    }
}
