<?php

declare(strict_types=1);

namespace App\Core\Filesystem\Dir;

/**
 * Base value object that represents an absolute directory path.
 */
abstract class Dir
{
    final protected function __construct(
        private readonly string $path
    ) {}

    /**
     * Return the absolute path, optionally appending a relative segment.
     *
     * @param  string  $relativePath  Optional relative path to append.
     */
    final public function path(string $relativePath = ''): string
    {
        if ($relativePath === '') {
            return $this->path;
        }

        return $this->path . DIRECTORY_SEPARATOR . ltrim(
            str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath),
            DIRECTORY_SEPARATOR
        );
    }

    /**
     * Return the absolute path to a file inside the directory.
     *
     * @param  string  $relativePath  File path relative to the directory.
     */
    final public function file(string $relativePath): string
    {
        return $this->path($relativePath);
    }

    /**
     * Determine whether the directory currently exists.
     */
    final public function exists(): bool
    {
        return is_dir($this->path);
    }
}
