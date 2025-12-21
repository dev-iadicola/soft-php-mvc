<?php

declare(strict_types=1);

namespace App\Core\Filesystem;

use App\Core\Contract\DriveInterface;
use App\Core\Exception\FileSystemException;

/**
 * Summary of Filesystem
 *
 * high-level filesystem abstraction taht delegates
 * file operations to a concrete Drive implementation.
 *
 * This class provides a simple and consistent API
 * to interact with difference dtorage backends
 */
class Filesystem
{
    /**
     * Summary of drive
     * The underlyng storage driver
     */
    protected DriveInterface $drive;

    public function __construct(DriveInterface $drive)
    {
        $this->drive = $drive;
    }

    /**
     * Summary of put
     * Write constest to a file
     *
     * @param  string  $path  The file relative to the disk root
     * @param  string  $content  the content to be writtingh
     * @param  array  $diskOptions  optional drive specific options
     */
    public function put(string $path, string $content, array $diskOptions = []): bool
    {
        return $this->drive->write($path, $content, $diskOptions);
    }

    /**
     * Read the contenst of a file
     */
    public function get(string $path): ?string
    {
        return $this->drive->read($path) ?? null;
    }

    /**
     * Delete a file
     */
    public function delete(string $path): void
    {
        $this->drive->delete($path);
    }

    /**
     * delete If Exist
     */
    public function deleteIfExist(string $path): bool
    {
        if ($this->exists($path)) {
            $this->delete($path);
            return true;
        }
        return false;
    }

    /**
     * Summary of delete Or Fail
     *
     * @throws FileSystemException
     */
    public function deleteOrFail(string $path): void
    {
        $this->existsOrFail($path);
        $this->delete($path);
    }

    /**
     * check if the file exists
     */
    public function exists(string $path): bool
    {
        return $this->drive->exists($path);
    }

    /**
     * check if the file exists Or Fail
     *
     * @throws FileSystemException
     */
    public function existsOrFail(string $path): void
    {
        if ( ! $this->exists($path)) {
            throw new FileSystemException("File {$path}  not found");
        }
    }

    /**
     * Get the absolute filesystem path to a file.
     *
     * @param  string  $path  The file path relative to the disk root.
     * @return string The absolute filesystem path.
     */
    public function path(string $path): string
    {
        return $this->drive->path($path);
    }
}
