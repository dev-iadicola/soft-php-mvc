<?php

declare(strict_types=1);

namespace App\Core\Inertia;

use JsonSerializable;

class InertiaPage implements JsonSerializable
{
    public function __construct(
        private string $component,
        private array $props,
        private string $url,
        private ?string $version = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'component' => $this->component,
            'props' => $this->props,
            'url' => $this->url,
            'version' => $this->version,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
