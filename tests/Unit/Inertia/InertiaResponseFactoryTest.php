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
            new InertiaAssetBundle($this->manifestPath, '/assets/build'),
            'frontend/app.tsx',
        );

        $factory->render('Preview/Welcome', [
            'meta' => ['title' => 'React Preview'],
        ]);

        $this->assertStringContainsString('/assets/build/assets/app.css', $response->getContent());
        $this->assertStringContainsString('/assets/build/assets/app.js', $response->getContent());
    }

    public function testItRendersSeoMetaTagsIntoHtmlRoot(): void
    {
        $_SERVER['REQUEST_URI'] = '/blog/test-article';

        $response = new Response($this->createMock(View::class));
        $factory = new InertiaResponseFactory($response, '1', 'app');

        $factory->render('Public/Blog/Show', [
            'meta' => ['title' => 'Test article | Iadicola // dev'],
            'seo' => [
                'title' => 'Test article | Iadicola // dev',
                'description' => 'Descrizione articolo',
                'canonical' => 'https://portfolio.test/blog/test-article',
                'image' => 'https://portfolio.test/assets/article.png',
                'type' => 'article',
                'robots' => 'index,follow',
                'site_name' => 'Iadicola // dev',
                'published_time' => '2026-03-31 10:00:00',
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => 'Test article',
                ],
            ],
        ]);

        $content = $response->getContent();

        $this->assertStringContainsString('<meta name="description" content="Descrizione articolo">', $content);
        $this->assertStringContainsString('<link rel="canonical" href="https://portfolio.test/blog/test-article">', $content);
        $this->assertStringContainsString('<meta property="og:type" content="article">', $content);
        $this->assertStringContainsString('<meta name="twitter:card" content="summary_large_image">', $content);
        $this->assertStringContainsString('<script type="application/ld+json">', $content);
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
