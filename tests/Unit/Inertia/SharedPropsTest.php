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

        $this->assertArrayNotHasKey('meta', $resolved);
    }
}
