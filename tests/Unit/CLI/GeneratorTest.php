<?php

declare(strict_types=1);

use App\Core\CLI\Commands\MakeControllerCommand;
use App\Core\CLI\Commands\MakeMiddlewareCommand;
use App\Core\CLI\Commands\MakeServiceCommand;
use App\Core\CLI\Commands\MakeRepositoryCommand;
use App\Core\CLI\Commands\MakeMigrationCommand;
use App\Core\CLI\Commands\MakeModelCommand;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    private string $root;
    private string $previousCwd;

    protected function setUp(): void
    {
        $this->previousCwd = getcwd();
        $this->root = sys_get_temp_dir() . '/soft-php-mvc-generator-' . uniqid('', true);

        mkdir($this->root, 0755, true);
        chdir($this->root);
    }

    protected function tearDown(): void
    {
        chdir($this->previousCwd);
        $this->deleteDir($this->root);
    }

    // ========================================================================
    // make:model
    // ========================================================================

    public function testMakeModelGeneratesFileWithCorrectContent(): void
    {
        $command = new MakeModelCommand();
        $command->exe(['php', 'make:model', 'Article']);

        $file = $this->root . '/app/Model/Article.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertStringContainsString('declare(strict_types=1)', $content);
        $this->assertStringContainsString('namespace App\Model', $content);
        $this->assertStringContainsString('class Article extends Model', $content);
    }

    // ========================================================================
    // make:controller
    // ========================================================================

    public function testMakeControllerGeneratesFileWithCorrectContent(): void
    {
        $command = new MakeControllerCommand();
        $command->exe(['php', 'make:controller', 'Blog']);

        $file = $this->root . '/app/Controllers/BlogController.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertStringContainsString('declare(strict_types=1)', $content);
        $this->assertStringContainsString('namespace App\Controllers', $content);
        $this->assertStringContainsString('class BlogController extends Controller', $content);
    }

    public function testMakeControllerDoesNotDuplicateSuffix(): void
    {
        $command = new MakeControllerCommand();
        $command->exe(['php', 'make:controller', 'BlogController']);

        $file = $this->root . '/app/Controllers/BlogController.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        // Should NOT have "BlogControllerController"
        $this->assertStringNotContainsString('BlogControllerController', $content);
    }

    public function testMakeControllerAddsPrefixAndMiddlewareAttributesWhenRequested(): void
    {
        $command = new MakeControllerCommand();
        $command->exe([
            'php',
            'make:controller',
            'Store',
            '--prefix=/admin',
            '--middleware=auth,verified',
        ]);

        $file = $this->root . '/app/Controllers/StoreController.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertStringContainsString("use App\\Core\\Http\\Attributes\\Prefix;", $content);
        $this->assertStringContainsString("use App\\Core\\Http\\Attributes\\Middleware;", $content);
        $this->assertStringContainsString("#[Prefix('/admin')]", $content);
        $this->assertStringContainsString("#[Middleware(['auth', 'verified'])]", $content);
    }

    public function testMakeControllerOmitsOptionalAttributesWhenNotConfigured(): void
    {
        $command = new MakeControllerCommand();
        $command->exe(['php', 'make:controller', 'PublicPage']);

        $file = $this->root . '/app/Controllers/PublicPageController.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertStringNotContainsString('Attributes\\Prefix', $content);
        $this->assertStringNotContainsString('Attributes\\Middleware', $content);
        $this->assertStringNotContainsString('#[Prefix(', $content);
        $this->assertStringNotContainsString('#[Middleware(', $content);
    }

    // ========================================================================
    // make:middleware (make:mw)
    // ========================================================================

    public function testMakeMiddlewareGeneratesFileWithCorrectContent(): void
    {
        $command = new MakeMiddlewareCommand();
        $command->exe(['php', 'make:mw', 'Auth']);

        $file = $this->root . '/app/Middleware/AuthMiddleware.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertStringContainsString('declare(strict_types=1)', $content);
        $this->assertStringContainsString('namespace App\Middleware', $content);
        $this->assertStringContainsString('class AuthMiddleware implements MiddlewareInterface', $content);
    }

    // ========================================================================
    // make:service
    // ========================================================================

    public function testMakeServiceGeneratesFileWithCorrectContent(): void
    {
        $command = new MakeServiceCommand();
        $command->exe(['php', 'make:service', 'Payment']);

        $file = $this->root . '/app/Services/PaymentService.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertStringContainsString('declare(strict_types=1)', $content);
        $this->assertStringContainsString('namespace App\Services', $content);
        $this->assertStringContainsString('class PaymentService', $content);
    }

    public function testMakeServiceDoesNotDuplicateSuffix(): void
    {
        $command = new MakeServiceCommand();
        $command->exe(['php', 'make:service', 'PaymentService']);

        $file = $this->root . '/app/Services/PaymentService.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertStringNotContainsString('PaymentServiceService', $content);
    }

    // ========================================================================
    // make:repository
    // ========================================================================

    public function testMakeRepositoryGeneratesFileWithCorrectContent(): void
    {
        $command = new MakeRepositoryCommand();
        $command->exe(['php', 'make:repository', 'User']);

        $file = $this->root . '/app/Repository/UserRepository.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertStringContainsString('declare(strict_types=1)', $content);
        $this->assertStringContainsString('namespace App\Repository', $content);
        $this->assertStringContainsString('class UserRepository extends BaseRepository', $content);
        $this->assertStringContainsString('User::class', $content);
    }

    // ========================================================================
    // make:migration
    // ========================================================================

    public function testMakeMigrationGeneratesFileWithCorrectContent(): void
    {
        $command = new MakeMigrationCommand();
        $command->exe(['php', 'make:migration', 'create_posts_table']);

        $migrationFiles = glob($this->root . '/Database/migration/*_create_posts_table.php');
        $this->assertNotEmpty($migrationFiles);
        $this->assertCount(1, $migrationFiles);

        $content = file_get_contents($migrationFiles[0]);
        $this->assertStringContainsString('declare(strict_types=1)', $content);
        // Table name should be guessed as "posts"
        $this->assertStringContainsString("'posts'", $content);
    }

    public function testMakeMigrationFileNameContainsTimestamp(): void
    {
        $command = new MakeMigrationCommand();
        $command->exe(['php', 'make:migration', 'create_tags_table']);

        $migrationFiles = glob($this->root . '/Database/migration/*_create_tags_table.php');
        $this->assertNotEmpty($migrationFiles);

        $fileName = basename($migrationFiles[0]);
        // Timestamp format: YYYY_MM_DD_HHiiss
        $this->assertMatchesRegularExpression('/^\d{4}_\d{2}_\d{2}_\d{6}_/', $fileName);
    }

    // ========================================================================
    // General: generated files should not overwrite existing ones
    // ========================================================================

    public function testMakeControllerDoesNotOverwriteExistingFile(): void
    {
        $command = new MakeControllerCommand();
        $command->exe(['php', 'make:controller', 'Existing']);

        $file = $this->root . '/app/Controllers/ExistingController.php';
        $originalContent = file_get_contents($file);

        // Run again — should not overwrite
        $command->exe(['php', 'make:controller', 'Existing']);
        $this->assertSame($originalContent, file_get_contents($file));
    }

    // ========================================================================
    // Helpers
    // ========================================================================

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
