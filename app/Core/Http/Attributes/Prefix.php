<?php

declare(strict_types=1);

namespace App\Core\Http\Attributes;

/**
 * Definisce un prefisso URL a livello di classe controller.
 *
 * Esempio: #[Prefix('/admin')]
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Prefix
{
    public string $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = '/' . ltrim(trim($prefix), '/');
    }
}
