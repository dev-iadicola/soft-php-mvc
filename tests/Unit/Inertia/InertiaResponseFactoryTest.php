<?php

declare(strict_types=1);

namespace Tests\Unit\Inertia;

use App\Core\Http\Response;
use App\Core\Inertia\InertiaAssetBundle;
use App\Core\Inertia\InertiaResponseFactory;
use App\Core\Inertia\SharedProps;
use App\Core\View;
use PHPUnit\Framework\TestCase;

class InertiaResponseFactoryTest extends TestCase
{
    private ?string $manifestPath = null;

    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_X_INERTIA'], $_SERVER['REQUEST_URI']);
        SharedProps::reset();
        if ($this->manifestPath !== null && is_file($this->manifestPath)) {
            unlink($this->manifestPath);
        }

        parent::tearDown();
    }

    public function testItBuildsHtmlRootResponseForFirstVisit(): void
    {
        $_SERVER['REQUEST_URI'] = '/admin/dashboard';

        $response = new Response($this->createMock(View::class));
        $factory = new InertiaResponseFactory($response, '1', 'app');

        $factory->render('Admin/Dashboard', [
            'meta' => ['title' => 'Dashboard'],
            'stats' => ['projects' => 4],
        ]);

        $this->assertStringContainsString('<div id="app" data-page="', $response->getContent());
        $this->assertStringContainsString('Admin/Dashboard', $response->getContent());
        $this->assertStringContainsString('/admin/dashboard', $response->getContent());
        $this->assertSame('text/html; charset=utf-8', $response->getHeaders()['Content-Type'] ?? null);
        $this->assertSame('1', $response->getHeaders()['X-Inertia-Version'] ?? null);
    }

    public function testItEmbedsBuiltAssetsInHtmlRootWhenManifestIsAvailable(): void
    {
        $_SERVER['REQUEST_URI'] = '/react-preview';
        $this->manifestPath = sys_get_temp_dir() . '/inertia-response-manifest-' . uniqid('', true) . '.json';

        file_put_contents($this->manifestPath, (string) json_encode([
            'frontend/app.tsx' => [
                'file' => 'assets/app.js',
                'css' => ['assets/app.css'],
            ],
        ], JSON_THROW_ON_ERROR));

        $response = new Response($this->createMock(View::class));
        $factory = new InertiaResponseFactory(
            $response,
            '1',
            'app',
            new InertiaAssetBundle($this->manifestPath, '/'),
            'frontend/app.tsx',
        );

        $factory->render('Preview/Welcome', [
            'meta' => ['title' => 'React Preview'],
        ]);

        $this->assertStringContainsString('/assets/app.css', $response->getContent());
        $this->assertStringContainsString('/assets/app.js', $response->getContent());
    }

    public function testItReturnsJsonPageObjectForInertiaRequests(): void
    {
        $_SERVER['HTTP_X_INERTIA'] = 'true';
        $_SERVER['REQUEST_URI'] = '/blog';

        $response = new Response($this->createMock(View::class));
        $factory = new InertiaResponseFactory($response, '1', 'app');

        $factory->render('Public/Blog/Index', [
            'meta' => ['title' => 'Blog'],
        ]);

        $payload = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('Public/Blog/Index', $payload['component']);
        $this->assertSame('/blog', $payload['url']);
        $this->assertSame('application/json; charset=utf-8', $response->getHeaders()['Content-Type'] ?? null);
        $this->assertSame('true', $response->getHeaders()['X-Inertia'] ?? null);
    }
}
