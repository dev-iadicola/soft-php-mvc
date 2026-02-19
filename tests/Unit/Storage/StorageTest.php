<?php

use PHPUnit\Framework\TestCase;
use App\Core\Filesystem\Filesystem;
use App\Core\Filesystem\LocalDrive;

class StorageTest extends TestCase
{
    private string $root;
    private Filesystem $fs;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir() . '/soft-php-mvc-storage-test-' . uniqid();
        $drive = new LocalDrive($this->root);
        $this->fs = new Filesystem($drive);
    }

    protected function tearDown(): void
    {
        $this->deleteDir($this->root);
    }

    public function testWriteReadExistsDelete(): void
    {
        $path = 'images/test.txt';
        $content = 'hello storage';

        $this->assertFalse($this->fs->exists($path));
        $this->assertTrue($this->fs->put($path, $content, ['visibility' => 'public']));
        $this->assertTrue($this->fs->exists($path));
        $this->assertSame($content, $this->fs->get($path));

        $this->fs->delete($path);
        $this->assertFalse($this->fs->exists($path));
    }

    public function testPathReturnsAbsolutePath(): void
    {
        $path = 'projects/example.jpg';
        $full = $this->fs->path($path);

        $this->assertStringContainsString($this->root, $full);
        $this->assertStringEndsWith(DIRECTORY_SEPARATOR . 'projects' . DIRECTORY_SEPARATOR . 'example.jpg', $full);
    }

    public function testDeleteIfExist(): void
    {
        $path = 'docs/readme.md';
        $this->assertFalse($this->fs->exists($path));

        $this->fs->put($path, 'content', ['visibility' => 'private']);
        $this->assertTrue($this->fs->exists($path));

        $this->assertTrue($this->fs->deleteIfExist($path));
        $this->assertFalse($this->fs->exists($path));

        $this->assertFalse($this->fs->deleteIfExist($path));
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
