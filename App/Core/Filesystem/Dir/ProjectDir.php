<?php

declare(strict_types=1);

namespace App\Core\Filesystem\Dir;

/**
 * Central access point for project-level directories.
 */
final class ProjectDir extends Dir
{
    /**
     * Create a directory object that points to the project root.
     */
    public static function instance(): self
    {
        return new self(dirname(__DIR__, 4));
    }
}
