<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Helpers\Log;
use App\Core\Http\Request;
use App\Core\Services\CsrfService;
use App\Core\Contract\MiddlewareInterface;

class CsrfMiddleware implements MiddlewareInterface
{   
    public function exec(Request $request): mixed
    {
        if (in_array($request->getRequestMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $csfr = new CsrfService();
            $sessToken = $csfr->getToken();
            $reqTok = $this->resolveRequestToken($request);

            if (!is_string($sessToken) || $sessToken === '' || !is_string($reqTok) || $reqTok === '' || !hash_equals($sessToken, $reqTok)) {
               Log::alert("Invalid CSRF: token in session = $sessToken, token in request = $reqTok");
                return response()->set419();
            }
        }

        return null;
    }

    private function resolveRequestToken(Request $request): ?string
    {
        $bodyToken = $request->get('_token');

        if (is_string($bodyToken) && $bodyToken !== '') {
            return $bodyToken;
        }

        return $request->getHeader('X-CSRF-TOKEN')
            ?? $request->getHeader('X-XSRF-TOKEN');
    }
}
