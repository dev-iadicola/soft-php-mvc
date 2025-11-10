<?php

namespace App\Core\Filesystem;

use App\Core\Contract\DriveInterface;

class Filesystem 
{
    protected DriveInterface $drive;

    public function __construct(DriveInterface $drive)
    {
        $this->drive = $drive;
    }

    // Write file content. 
    public function put(string $path, string $content, array $diskOptions = []):bool{
        return $this->drive->write($path, $content, $diskOptions);
    }

    public function get(string $path):string{
        return $this->drive->read($path);
    }

    public function delete(string $path){
        $this->drive->delete($path);
    }

    public function exists(string $path):bool{
        return $this->drive->exists($path);
    }

    public function path(string $path):string{
        return $this->drive->path($path);
    }

}
