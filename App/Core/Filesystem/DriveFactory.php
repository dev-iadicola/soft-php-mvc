<?php

namespace App\Core\Filesystem;

use App\Core\Exception\StorageException;
/**
 * DriveFacotory
 * Here we create all class for specific Drive with interface DriveInterface 
 * for moment we use just LocalDrive.
 */
class  DriveFactory
{
    public static function create(string $driveName, array $config){

        return match($driveName){
            'local' => new LocalDrive($config['root']),
            default => throw new StorageException("Unsupported drive> $driveName"),
        };
    }    
}
