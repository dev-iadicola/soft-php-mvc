<?php

use PHPUnit\Framework\TestCase;
use App\Core\Filesystem\StorageManager;
use App\Core\Exception\StorageException;

class StorageManagerTest extends TestCase
{
    public function testMissingDisksKeyThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StorageManager([]);
    }

    public function testUnknownDiskThrows(): void
    {
        $config = [
            'disks' => [
                'public' => [
                    'driver' => 'local',
                    'root' => sys_get_temp_dir(),
                ],
            ],
        ];

        $manager = new StorageManager($config);
        $this->expectException(StorageException::class);
        $manager->disk('missing');
    }
}
