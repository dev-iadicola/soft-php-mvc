<?php

declare(strict_types=1);

namespace App\Core\Filesystem\Dir;

/**
 * Directory object for the storage tree.
 */
final class StorageDir extends Dir
{
    /**
     * Create a directory object that points to the storage root.
     */
    public static function instance(): self
    {
        return new self(ProjectDir::instance()->path('storage'));
    }

    /**
     * Return the absolute path to the logs directory.
     */
    public function logs(): string
    {
        return $this->path('logs');
    }
}
