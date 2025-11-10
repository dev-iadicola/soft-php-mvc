<?php

namespace   App\Core\Filesystem;

use App\Core\Contract\DriveInterface;
use App\Core\Helpers\Path;
use InvalidArgumentException;

/**
 * Implementation of the DriveInterface for the local filesystem. 
 * Handles file read/write operation and permission management. 
 */
class LocalDrive implements DriveInterface
{
    protected string $root;
    protected string $dir;

    protected array $permissionMap = [
        'file' => ['public' => 0644, 'private' => 0600],
        'dir' => ['public' => 0755, 'private' => 0700],
    ];

    public function __construct(string $root)
    {
        $this->root = rtrim($root, DIRECTORY_SEPARATOR);
    }

    protected function getFullPath(string $path): string
    {
        return $this->root .DIRECTORY_SEPARATOR. Path::normalize($path);
    }
    public function write(string $path, string $content, array $diskOptions): bool
    {
        $full = $this->getFullPath($path);
        $dir = dirname($full);
        $permissionDirArray = $this->permissionMap['dir'];
        $diskVisibility = $diskOptions['visibility'] ?? 'private';
        if (!is_dir($dir)) {
            // * Validate string permission
            if (!isset( $permissionDirArray[$diskVisibility])) {
                throw new InvalidArgumentException("Invalid mode visibility in your disk: $diskVisibility for directory $dir. You can choose 'public' or 'private'.");
            }
            // * Create direcotry with permission
            mkdir($dir, $this->permissionMap['dir'][$diskVisibility], true);
        }
        file_put_contents($full, $content);

        $this->setVisibility($full, $diskVisibility);

        return true;
    }

    public function read(string $path): string
    {
        $full = $this->getFullPath($path);
        if (!file_exists($full)) {
            throw new InvalidArgumentException("File $full don't exist in storage dir.");
        }
        return file_get_contents($full);
    }

    public function exists(string $path): bool
    {
        return file_exists($this->getFullPath($path));
    }

    public function setVisibility(string $path, string $visibility): bool
    {
        return chmod($path, $this->permissionMap['file'][$visibility]);
    }


    public function path(string $path): string
    {
       return $this->getFullPath($path);
    }

    public function delete(string $path): bool
    {
        $full = $this->getFullPath($path);
        return file_exists($full) ? unlink($full) : false;
    }
}
