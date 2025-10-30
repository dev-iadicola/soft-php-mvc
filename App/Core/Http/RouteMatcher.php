<?php
// App/Core/Http/RouteMatcher.php
namespace App\Core\Http;

/**
 * Trova la rotta corrispondente.
 * Converte "/users/{id}" in una regex e mappa i parametri catturati.
 */
class RouteMatcher
{
    /**
     * @return array|null rotta + ['params'=>['id'=>123]] se trovata, altrimenti null
     */
    public function match(string $uri, string $method, RouteRegister $registry): ?array
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        $routes = $registry->all()[$method] ?? [];

        foreach ($routes as $route) {
            [$regex, $paramNames] = $this->compilePattern($route['path']);

            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches); // rimuove il match completo
                $paramsAssoc = [];
                foreach ($matches as $i => $val) {
                    $paramsAssoc[$paramNames[$i]] = $val;
                }
                $route['params'] = $paramsAssoc;
                return $route;
            }
        }

        return null;
    }

    /**
     * Trasforma "/users/{id}/{slug}" in:
     *  - regex: "~^/users/([^/]+)/([^/]+)$~"
     *  - paramNames: ['id','slug']
     */
    private function compilePattern(string $path): array
    {
        $paramNames = [];
        $regex = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', function ($m) use (&$paramNames) {
            $paramNames[] = $m[1];
            return '([^/]+)'; // segment matcher
        }, $path);

        return ['~^' . $regex . '$~', $paramNames];
    }
}
