<?php

declare(strict_types=1);

namespace App\Core\Http\Attributes;

/**
 * Definisce un prefisso per i nomi delle rotte a livello di classe controller.
 *
 * Esempio: #[NamePrefix('admin.')]
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class NamePrefix
{
    public string $namePrefix;

    public function __construct(string $namePrefix)
    {
        $this->namePrefix = $namePrefix;
    }
}
