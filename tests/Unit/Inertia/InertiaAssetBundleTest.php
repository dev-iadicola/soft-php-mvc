<?php

declare(strict_types=1);

namespace Tests\Unit\Inertia;

use App\Core\Inertia\InertiaAssetBundle;
use PHPUnit\Framework\TestCase;

class InertiaAssetBundleTest extends TestCase
{
    private string $manifestPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manifestPath = sys_get_temp_dir() . '/inertia-manifest-' . uniqid('', true) . '.json';
    }

    protected function tearDown(): void
    {
        if (is_file($this->manifestPath)) {
            unlink($this->manifestPath);
        }

        parent::tearDown();
    }

    public function testItRendersStylesAndScriptFromManifest(): void
    {
        file_put_contents($this->manifestPath, (string) json_encode([
            'frontend/app.tsx' => [
                'file' => 'assets/app.js',
                'css' => ['assets/app.css'],
                'imports' => ['_shared.js'],
            ],
            '_shared.js' => [
                'file' => 'assets/shared.js',
                'css' => ['assets/shared.css'],
            ],
        ], JSON_THROW_ON_ERROR));

        $bundle = new InertiaAssetBundle($this->manifestPath, '/');
        $tags = $bundle->renderTags('frontend/app.tsx');

        $this->assertStringContainsString('/assets/app.css', $tags);
        $this->assertStringContainsString('/assets/shared.css', $tags);
        $this->assertStringContainsString('/assets/app.js', $tags);
    }

    public function testItReturnsEmptyStringWhenManifestIsMissing(): void
    {
        $bundle = new InertiaAssetBundle($this->manifestPath, '/');

        $this->assertSame('', $bundle->renderTags('frontend/app.tsx'));
    }
}
