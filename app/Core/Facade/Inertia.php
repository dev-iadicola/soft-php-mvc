<?php

declare(strict_types=1);

namespace App\Core\Facade;

use App\Core\Inertia\InertiaResponseFactory;
use App\Core\Inertia\SharedProps;
use App\Core\Http\Response;

class Inertia
{
    public static function render(string $component, array $props = []): Response
    {
        return self::factory()->render($component, $props);
    }

    public static function share(string|array $key, mixed $value = null): void
    {
        SharedProps::share($key, $value);
    }

    public static function flush(): void
    {
        SharedProps::reset();
    }

    private static function factory(): InertiaResponseFactory
    {
        $version = (string) (mvc()?->config?->get('inertia.version') ?? '1');
        $rootElementId = (string) (mvc()?->config?->get('inertia.root_element_id') ?? 'app');

        return new InertiaResponseFactory(
            response: mvc()->response,
            version: $version,
            rootElementId: $rootElementId,
        );
    }
}
