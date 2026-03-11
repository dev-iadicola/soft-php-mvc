<?php

declare(strict_types=1);

namespace App\Core\Http\Attributes;

/**
 * Definisce middleware a livello di classe controller.
 *
 * Esempio: #[Middleware(['auth', 'admin'])]
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Middleware
{
    /** @var string[] */
    public array $middleware;

    /**
     * @param string[]|string $middleware
     */
    public function __construct(array|string $middleware)
    {
        $this->middleware = (array) $middleware;
    }
}
