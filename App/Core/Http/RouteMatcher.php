<?php
// App/Core/Http/RouteMatcher.php
namespace App\Core\Http;

use App\Core\Exception\NotFoundException;
use App\Utils\Casting;

/**
 * Trova la rotta corrispondente.
 * Converte "/users/{id}" in una regex e mappa i parametri catturati.
 */
class RouteMatcher
{
    /**
     * @return array|null rotta + ['params'=>['id'=>123]] se trovata, altrimenti null
     */
    public function match(Request $request, RouteRegister $registry): ?array
    {

        // * Rirtona un array di routes con il metodo di richiesta. es GET se la richiesta è GET.
        $routes = $registry->getByRequestMethod($request);



        foreach ($routes as $route) {
            // Compila il pattern della rotta (es: /user/{id})
            $compiled = $this->compilePattern($route['path']);
            $regex = $compiled[0];
            $paramNames = $compiled[1];

            // Controlla se la richiesta corrisponde alla rotta
            if (preg_match($regex, $request->uri(), $matches)) {
                // Rimuove il primo indice dell'array che in questo caso sarebbe progetto/id a //(cancellatoprogetto)/id ritornando solo l'id e il resto.
                array_shift($matches);
                // Converte i valori in tipi corretti (int, bool, ecc.)
                $matches = Casting::formatArray($matches);

                // Associa i parametri trovati ai loro nomi
                $paramsAssoc = [];
                foreach ($matches as $index => $value) {
                    if (isset($paramNames[$index])) {
                        $paramName = $paramNames[$index];
                        $paramsAssoc[$paramName] = $value;
                    }
                }

                // Aggiunge i parametri elaborati alla rotta
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
