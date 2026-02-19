<?php

use PHPUnit\Framework\TestCase;
use App\Core\Filesystem\DriveFactory;
use App\Core\Exception\StorageException;
use App\Core\Filesystem\LocalDrive;

class DriveFactoryTest extends TestCase
{
    public function testCreateLocalDrive(): void
    {
        $drive = DriveFactory::create('local', ['root' => sys_get_temp_dir()]);
        $this->assertInstanceOf(LocalDrive::class, $drive);
    }

    public function testUnsupportedDriveThrows(): void
    {
        $this->expectException(StorageException::class);
        DriveFactory::create('unknown', ['root' => sys_get_temp_dir()]);
    }
}
