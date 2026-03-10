<?php

declare(strict_types=1);

namespace App\Core\Filesystem\Dir;

/**
 * Directory object for the framework core source tree.
 */
final class CoreDir extends Dir
{
    /**
     * Create a directory object that points to App/Core.
     */
    public static function instance(): self
    {
        return new self(ProjectDir::instance()->path('App/Core'));
    }
}
