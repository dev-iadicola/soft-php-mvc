<?php

namespace App\Middleware;

use RuntimeException;
use App\Core\Helpers\Log;
use App\Core\Http\Request;
use App\Core\Contract\MiddlewareInterface;

class CorsMiddleware implements MiddlewareInterface
{
    private array $allowedOrigin = [];
    public function exec(Request $request)
    {

        $this->allowedOrigin = mvc()->config->settings['allowed-origin'] ?? throw new RuntimeException("
            Your configutaion file 'config/settings.php' dont have the key 'allowed-origin' in array.
        ");

        if (empty($this->allowedOrigin)) {
            throw new RuntimeException("
            Your configuration file 'config/settings.php' has nothing inside the key 'allowed-origin'
            ");
        }

        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        // check the origin is autorized. 
        $isAllowed = false;
        foreach ($this->allowedOrigin as $allowed) {
            if ($allowed === $origin || fnmatch($allowed, $origin)) {
                $isAllowed = true;
            }
        }

        if ($isAllowed) {
            header("Access-Control-Allow-Origin: $origin");
            header('Access-Control-Allow-Credentials: true');
        } else {
            // Logga l’origine bloccata
            Log::info("CORS blocked for origin: $origin");

            // Puoi rispondere subito con 403
            http_response_code(403);
            echo json_encode([
                'error' => 'Origin not allowed',
                'origin' => $origin
            ]);
            exit;
        }

         // Header common to all CORS response
         header('Vary: Origin');
         header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
         header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
 
         // Managing preflight
         if ($request->getRequestMethod() === 'OPTIONS') {
             http_response_code(204);
             exit;
         }
 
    }
}
