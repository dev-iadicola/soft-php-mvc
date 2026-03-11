<?php

declare(strict_types=1);

use App\Core\CLI\Commands\MakeModelCommand;
use PHPUnit\Framework\TestCase;

class MakeModelCommandTest extends TestCase
{
    private string $root;
    private string $previousCwd;

    protected function setUp(): void
    {
        $this->previousCwd = getcwd();
        $this->root = sys_get_temp_dir() . '/soft-php-mvc-make-model-' . uniqid('', true);

        mkdir($this->root, 0755, true);
        chdir($this->root);
    }

    protected function tearDown(): void
    {
        chdir($this->previousCwd);
        $this->deleteDir($this->root);
    }

    public function testMakeModelCreatesTypedModelStub(): void
    {
        $command = new MakeModelCommand();
        $command->exe(['php', 'make:model', 'BlogPost']);

        $file = $this->root . '/app/Model/BlogPost.php';

        $this->assertFileExists($file);
        $content = file_get_contents($file);

        $this->assertStringContainsString("class BlogPost extends Model", $content);
        $this->assertStringContainsString("protected string \$table = 'blogposts';", $content);
        $this->assertStringContainsString('protected ?int $id = null;', $content);
    }

    public function testMakeModelSupportsMigrationResourceAndCustomTable(): void
    {
        $command = new MakeModelCommand();
        $command->exe([
            'php',
            'make:model',
            'AuditLog',
            '--table=audit_logs',
            '--migration',
            '--resource',
        ]);

        $this->assertFileExists($this->root . '/app/Model/AuditLog.php');
        $this->assertFileExists($this->root . '/app/Controllers/AuditLogController.php');

        $migrationFiles = glob($this->root . '/Database/migration/*_create_audit_logs_table.php');
        $this->assertNotFalse($migrationFiles);
        $this->assertCount(1, $migrationFiles);
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
                continue;
            }

            @unlink($path);
        }

        @rmdir($dir);
    }
}
