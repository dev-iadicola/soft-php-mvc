<?php

namespace App\Core\Filesystem;

use App\Core\Exception\StorageException;
use App\Core\Storage;
use InvalidArgumentException;

class StorageManager
{
    private array $disks;
    public function __construct(protected array $config)
    {
        if (!(array_key_exists('disks', $this->config))) {
            throw new InvalidArgumentException("Your file `filesystem.php` in directory `config` don't have key 'disks' ");
        }
        $this->disks = $this->config['disks'];
    }

    // Get a disk by tits name and return Filesystem instance. 
    public function disk(string $diskName)
    {
        if (!isset( $this->disks[$diskName])) {
            throw new  StorageException("Error, disk name $diskName don't conmfigurate in file `filesystem.php` .");
        }

        $diskConfig = $this->disks[$diskName];
        $driveName = $diskConfig['driver'] ?? 'local';

        $drive = DriveFactory::create($driveName, $diskConfig);

        return new Filesystem($drive);
    }
}
