<?php

declare(strict_types=1);

namespace Tests\Unit\Inertia;

use App\Core\Inertia\InertiaPage;
use PHPUnit\Framework\TestCase;

class InertiaPageTest extends TestCase
{
    public function testItSerializesToExpectedPageShape(): void
    {
        $page = new InertiaPage(
            component: 'Admin/Dashboard',
            props: ['stats' => ['projects' => 4]],
            url: '/admin/dashboard',
            version: '1',
        );

        $this->assertSame([
            'component' => 'Admin/Dashboard',
            'props' => ['stats' => ['projects' => 4]],
            'url' => '/admin/dashboard',
            'version' => '1',
        ], $page->toArray());

        $this->assertSame($page->toArray(), $page->jsonSerialize());
    }
}
