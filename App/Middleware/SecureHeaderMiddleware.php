<?php

namespace App\Middleware;

use App\Core\Http\Request;
use App\Core\Contract\MiddlewareInterface;

class SecureHeaderMiddleware implements MiddlewareInterface
{
    public function exec(Request $request)
    {
        header("X-Frame-Options: SAMEORIGIN");        // Impedisce di incorporare la tua pagina in un <iframe> su un altro dominio.
        header("X-Content-Type-Options: nosniff");    //  Blocca il browser dal tentare di "indovinare" il tipo MIME.
        header("Referrer-Policy: no-referrer-when-downgrade"); //  Nasconde parte del referrer (l’URL sorgente) quando si passa da HTTPS a HTTP.
        header("X-XSS-Protection: 1; mode=block");    // Abilita (per browser legacy) la protezione base anti-XSS.
        header("Permissions-Policy: geolocation=(), microphone=()"); //  Disabilita l’uso automatico di funzioni come geolocalizzazione o microfono.

    }
}
