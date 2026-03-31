<?php

use PHPUnit\Framework\TestCase;
use App\Core\Filesystem\Filesystem;
use App\Core\Filesystem\LocalDrive;

class FilesystemTest extends TestCase
{
    private string $root;
    private Filesystem $fs;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir() . '/soft-php-mvc-filesystem-test-' . uniqid();
        $this->fs = new Filesystem(new LocalDrive($this->root));
    }

    protected function tearDown(): void
    {
        $this->deleteDir($this->root);
    }

    public function testPutGetExistsDelete(): void
    {
        $path = 'foo/bar.txt';
        $this->assertFalse($this->fs->exists($path));

        $this->assertTrue($this->fs->put($path, 'data', ['visibility' => 'public']));
        $this->assertTrue($this->fs->exists($path));
        $this->assertSame('data', $this->fs->get($path));

        $this->fs->delete($path);
        $this->assertFalse($this->fs->exists($path));
    }

    public function testDeleteIfExist(): void
    {
        $path = 'a/b/c.txt';
        $this->assertFalse($this->fs->deleteIfExist($path));

        $this->fs->put($path, 'x', ['visibility' => 'private']);
        $this->assertTrue($this->fs->deleteIfExist($path));
        $this->assertFalse($this->fs->exists($path));
    }

    public function testPath(): void
    {
        $path = 'img/test.png';
        $full = $this->fs->path($path);
        $this->assertStringContainsString($this->root, $full);
    }

    public function testGetPathPublicDisk(): void
    {
        $fs = new Filesystem(new LocalDrive($this->root), 'public', '/storage');
        $this->assertSame('/storage/images/test.png', $fs->getPath('images/test.png'));
    }

    public function testGetPathPrivateDisk(): void
    {
        $fs = new Filesystem(new LocalDrive($this->root), 'private', '/storage');
        $this->assertSame('images/test.png', $fs->getPath('images/test.png'));
    }

    public function testGetPathStripsLeadingSlash(): void
    {
        $fs = new Filesystem(new LocalDrive($this->root), 'public', '/storage');
        $this->assertSame('/storage/images/test.png', $fs->getPath('/images/test.png'));
    }

    public function testOverwriteExistingFile(): void
    {
        $path = 'overwrite/test.txt';
        $this->fs->put($path, 'original', ['visibility' => 'public']);
        $this->assertSame('original', $this->fs->get($path));

        $this->fs->put($path, 'updated', ['visibility' => 'public']);
        $this->assertSame('updated', $this->fs->get($path));
    }

    public function testDeleteOrFailOnNonExistentFile(): void
    {
        $this->expectException(\App\Core\Exception\FileSystemException::class);
        $this->fs->deleteOrFail('non/existent/file.txt');
    }

    public function testDeleteOrFailOnExistingFile(): void
    {
        $path = 'deletable/file.txt';
        $this->fs->put($path, 'data', ['visibility' => 'public']);
        $this->assertTrue($this->fs->exists($path));

        $this->fs->deleteOrFail($path);
        $this->assertFalse($this->fs->exists($path));
    }

    public function testExistsOrFailOnNonExistentFile(): void
    {
        $this->expectException(\App\Core\Exception\FileSystemException::class);
        $this->fs->existsOrFail('missing.txt');
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
