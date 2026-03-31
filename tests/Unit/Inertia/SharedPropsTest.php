<?php

declare(strict_types=1);

namespace Tests\Unit\Inertia;

use App\Core\Inertia\SharedProps;
use PHPUnit\Framework\TestCase;

class SharedPropsTest extends TestCase
{
    protected function tearDown(): void
    {
        SharedProps::reset();

        parent::tearDown();
    }

    public function testItMergesSharedPropsWithPageProps(): void
    {
        SharedProps::share('app', ['name' => 'Soft MVC']);
        SharedProps::share('meta', fn (): array => ['title' => 'Shared title']);

        $resolved = SharedProps::resolve([
            'meta' => ['title' => 'Page title'],
            'stats' => ['projects' => 8],
        ]);

        $this->assertSame('Soft MVC', $resolved['app']['name']);
        $this->assertSame('Page title', $resolved['meta']['title']);
        $this->assertSame(8, $resolved['stats']['projects']);
    }

    public function testResetClearsCustomSharedProps(): void
    {
        SharedProps::share('meta', ['title' => 'Before reset']);
        SharedProps::reset();

        $resolved = SharedProps::resolve();

        $this->assertArrayHasKey('meta', $resolved);
        $this->assertNotSame('Before reset', $resolved['meta']['title']);
    }

    public function testItProvidesDefaultGuestSharedProps(): void
    {
        $_SERVER['HTTP_HOST'] = 'portfolio.test';
        $_SERVER['REQUEST_URI'] = '/react-preview';

        $resolved = SharedProps::resolve();

        $this->assertSame('Iadicola // dev', $resolved['app']['name']);
        $this->assertSame('/react-preview', $resolved['routing']['current']);
        $this->assertSame('http://portfolio.test/react-preview', $resolved['routing']['canonical']);
        $this->assertIsArray($resolved['navigation']['main']);
        $this->assertSame('Home', $resolved['navigation']['main'][0]['label']);
        $this->assertSame('http://portfolio.test/react-preview', $resolved['seo']['canonical']);
        $this->assertSame('index,follow', $resolved['seo']['robots']);
        $this->assertSame('Iadicola // dev', $resolved['seo']['site_name']);
        $this->assertSame('website', $resolved['seo']['type']);
    }
}
