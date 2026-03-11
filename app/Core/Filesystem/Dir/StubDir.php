<?php

declare(strict_types=1);

namespace App\Core\Filesystem\Dir;

/**
 * Directory object for CLI stub templates.
 */
final class StubDir extends Dir
{
    /**
     * Create a directory object that points to App/Core/CLI/Stubs.
     */
    public static function instance(): self
    {
        return new self(CoreDir::instance()->path('CLI/Stubs'));
    }
}
