<?php

declare(strict_types=1);

namespace App\Core\Http\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Put extends RouteAttribute
{
    public function __construct(
        string $path,
        ?string $name = null,
        array|string|null $middleware = null,
    ) {
        parent::__construct($path, 'PUT', $name, $middleware);
    }
}
