<?php

declare(strict_types=1);

namespace App\Core\Filesystem\Dir;

/**
 * Directory object for application views.
 */
final class ViewDir extends Dir
{
    /**
     * Create a directory object that points to the views root.
     */
    public static function instance(): self
    {
        return new self(ProjectDir::instance()->path('views'));
    }
}
