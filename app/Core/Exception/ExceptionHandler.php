<?php

declare(strict_types=1);

namespace App\Core\Exception;

use App\Core\Http\Response;
use App\Core\Helpers\Log;
use Throwable;

class ExceptionHandler
{
    public static function handle(Throwable $e, Response $response): void
    {
        $code = match (true) {
            $e instanceof NotFoundException => 404,
            $e instanceof ValidationException => 422,
            $e instanceof UnauthorizedException => 401,
            default => ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500,
        };

        if ($code >= 500) {
            Log::exception($e);
        }

        if ($response->wantsJson()) {
            $payload = ['error' => $e->getMessage(), 'code' => $code];

            if ($e instanceof ValidationException) {
                $payload['errors'] = $e->getErrors();
            }

            $response->json($payload, $code);
            return;
        }

        $response->setErrorHandle($e->getMessage(), $code);
    }
}
