<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Contract\MiddlewareInterface;
use App\Core\Http\Request;
use App\Services\VisitorService;

class VisitorTrackingMiddleware implements MiddlewareInterface
{
    /**
     * Registra la visita dell'utente nel database.
     *
     * Esclude rotte admin, asset statici, login/logout e bot noti
     * per registrare solo le visite reali alle pagine pubbliche.
     */
    public function exec(Request $request): mixed
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        if ($this->shouldSkip($path)) {
            return null;
        }

        try {
            VisitorService::create([
                'ip_address' => $this->getClientIp(),
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
                'url'        => substr($path, 0, 500),
                'session_id' => session_id() ?: null,
            ]);
        } catch (\Throwable) {
            // Non bloccare la richiesta se il tracking fallisce
        }

        return null;
    }

    private function shouldSkip(string $path): bool
    {
        // Escludi rotte admin
        if (str_starts_with($path, '/admin')) {
            return true;
        }

        // Escludi login/logout/register
        if (in_array($path, ['/login', '/logout', '/register'], true)) {
            return true;
        }

        // Escludi asset statici
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if (in_array($extension, ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'map'], true)) {
            return true;
        }

        return false;
    }

    private function getClientIp(): string
    {
        $headers = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $header) {
            $value = $_SERVER[$header] ?? null;
            if ($value !== null && $value !== '') {
                // X-Forwarded-For puo contenere piu IP separati da virgola
                $ip = trim(explode(',', $value)[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }
}
