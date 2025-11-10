<?php

namespace App\Core\Facade;

use App\Core\Mvc;
use App\Core\Filesystem\StorageManager;

/**
 * class Storage
 * Static entry point to access the StorageManare.
 * Simplifies disk selection and file operations.
 * Pattern: Faacde.
 */
class Storage
{
    public static function make(string $diskName){
        $manager = new StorageManager(Mvc::$mvc->config->filesystem);
        return $manager->disk($diskName);
    }
}
