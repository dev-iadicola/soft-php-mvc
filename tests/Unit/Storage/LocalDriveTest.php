<?php

use PHPUnit\Framework\TestCase;
use App\Core\Filesystem\LocalDrive;
use App\Core\Exception\StorageException;

class LocalDriveTest extends TestCase
{
    private string $root;
    private LocalDrive $drive;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir() . '/soft-php-mvc-localdrive-test-' . uniqid();
        $this->drive = new LocalDrive($this->root);
    }

    protected function tearDown(): void
    {
        $this->deleteDir($this->root);
    }

    public function testWriteCreatesDirectoryAndFile(): void
    {
        $path = 'nested/dir/file.txt';
        $content = 'hello';

        $this->assertTrue($this->drive->write($path, $content, ['visibility' => 'public']));

        $full = $this->drive->path($path);
        $this->assertFileExists($full);
        $this->assertSame($content, file_get_contents($full));
    }

    public function testReadThrowsIfMissing(): void
    {
        $this->expectException(StorageException::class);
        $this->drive->read('missing.txt');
    }

    public function testExistsAndDelete(): void
    {
        $path = 'file.txt';
        $this->assertFalse($this->drive->exists($path));

        $this->drive->write($path, 'x', ['visibility' => 'private']);
        $this->assertTrue($this->drive->exists($path));

        $this->assertTrue($this->drive->delete($path));
        $this->assertFalse($this->drive->exists($path));
    }

    public function testInvalidVisibilityThrows(): void
    {
        $this->expectException(StorageException::class);
        $this->drive->write('file.txt', 'x', ['visibility' => 'weird']);
    }

    public function testPathReturnsAbsolutePath(): void
    {
        $path = 'a/b/c.txt';
        $full = $this->drive->path($path);
        $this->assertStringContainsString($this->root, $full);
        $this->assertStringEndsWith(DIRECTORY_SEPARATOR . 'a' . DIRECTORY_SEPARATOR . 'b' . DIRECTORY_SEPARATOR . 'c.txt', $full);
    }

    private function deleteDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = scandir($dir);
        if ($items === false) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}
