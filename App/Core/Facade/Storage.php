<?php

namespace App\Core\Facade;

use App\Core\Mvc;
use App\Core\Filesystem\Filesystem;
use App\Core\Filesystem\StorageManager;

/**
 * class Storage
 * Static entry point to access the StorageManare.
 * Simplifies disk selection and file operations.
 * Pattern: Faacde.
 */
class Storage
{
    public static function make(string $diskName): Filesystem{
        $manager = new StorageManager(Mvc::$mvc->config->filesystem);
        return $manager->disk($diskName);
    }

    /**
     * Build a DB-safe path for a stored file.
     * For public disks, returns a URL-friendly path (e.g. "/storage/..").
     */
    public static function dbPath(string $path, string $diskName = 'public'): string
    {
        $path = ltrim($path, '/');
        $disks = Mvc::$mvc->config->filesystem['disks'] ?? [];
        $disk = $disks[$diskName] ?? [];
        $visibility = $disk['visibility'] ?? null;

        if ($visibility === 'public' || $diskName === 'public') {
            return '/storage/' . $path;
        }

        return $path;
    }
}
