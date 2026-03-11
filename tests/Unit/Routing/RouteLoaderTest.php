<?php

declare(strict_types=1);

use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Attributes\RouteAttribute;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Post;
use App\Core\Http\Attributes\Put;
use App\Core\Http\Attributes\Delete;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\NamePrefix;
use App\Core\Http\Helpers\RouteCollection;
use App\Core\Http\Helpers\RouteDefinition;
use App\Core\Http\Helpers\RouteHelper;
use PHPUnit\Framework\TestCase;

// ---- Fixture controllers per i test (definiti in fondo al file) ----

class RouteLoaderTest extends TestCase
{
    // ========================================================================
    // Test sugli attributi Spatie-style
    // ========================================================================

    public function testGetAttributeCreatesCorrectMethod(): void
    {
        $attr = new Get('/users', name: 'users.index');
        $this->assertSame('GET', $attr->method);
        $this->assertSame('/users', $attr->path);
        $this->assertSame('users.index', $attr->name);
    }

    public function testPostAttributeCreatesCorrectMethod(): void
    {
        $attr = new Post('/users');
        $this->assertSame('POST', $attr->method);
        $this->assertSame('/users', $attr->path);
    }

    public function testPutAttributeCreatesCorrectMethod(): void
    {
        $attr = new Put('/users/{id}');
        $this->assertSame('PUT', $attr->method);
        $this->assertSame('/users/{id}', $attr->path);
    }

    public function testDeleteAttributeCreatesCorrectMethod(): void
    {
        $attr = new Delete('/users/{id}');
        $this->assertSame('DELETE', $attr->method);
    }

    public function testRouteAttributeNormalizesPath(): void
    {
        $attr = new Get('users');
        $this->assertSame('/users', $attr->path);

        $attr2 = new Get('admin.users');
        $this->assertSame('/admin/users', $attr2->path);
    }

    public function testRouteAttributeAddsWebMiddlewareByDefault(): void
    {
        $attr = new Get('/test');
        $this->assertContains('web', $attr->middleware);
    }

    public function testRouteAttributePreservesCustomMiddleware(): void
    {
        $attr = new Get('/test', middleware: ['auth', 'admin']);
        $this->assertContains('web', $attr->middleware);
        $this->assertContains('auth', $attr->middleware);
        $this->assertContains('admin', $attr->middleware);
    }

    public function testRouteAttributeDoesNotDuplicateWebMiddleware(): void
    {
        $attr = new Get('/test', middleware: ['web', 'auth']);
        $count = array_count_values($attr->middleware);
        $this->assertSame(1, $count['web']);
    }

    // ========================================================================
    // Test sugli attributi di classe
    // ========================================================================

    public function testPrefixAttribute(): void
    {
        $prefix = new Prefix('/admin');
        $this->assertSame('/admin', $prefix->prefix);

        $prefix2 = new Prefix('api');
        $this->assertSame('/api', $prefix2->prefix);
    }

    public function testMiddlewareAttributeWithArray(): void
    {
        $mw = new Middleware(['auth', 'admin']);
        $this->assertSame(['auth', 'admin'], $mw->middleware);
    }

    public function testMiddlewareAttributeWithString(): void
    {
        $mw = new Middleware('auth');
        $this->assertSame(['auth'], $mw->middleware);
    }

    public function testNamePrefixAttribute(): void
    {
        $np = new NamePrefix('admin.');
        $this->assertSame('admin.', $np->namePrefix);
    }

    // ========================================================================
    // Test sulla compatibilita legacy RouteAttr
    // ========================================================================

    public function testLegacyRouteAttrStillWorks(): void
    {
        $attr = new RouteAttr('/dashboard', 'GET', 'admin.dashboard');
        $this->assertSame('/dashboard', $attr->path);
        $this->assertSame('GET', $attr->method);
        $this->assertSame('admin.dashboard', $attr->name);
        $this->assertContains('web', $attr->middleware);
    }

    public function testLegacyRouteAttrWithPostMethod(): void
    {
        $attr = new RouteAttr('/login', 'POST', 'login');
        $this->assertSame('POST', $attr->method);
    }

    public function testLegacyRouteAttrNormalizesPath(): void
    {
        $attr = new RouteAttr('admin.dashboard');
        $this->assertSame('/admin/dashboard', $attr->path);
    }

    // ========================================================================
    // Test URL generation (RouteHelper)
    // ========================================================================

    public function testRouteHelperGeneratesSimpleUrl(): void
    {
        $collection = new RouteCollection();
        $collection->add(new RouteDefinition('/dashboard', 'GET', 'Ctrl', 'index', 'dashboard'));

        RouteHelper::setRouteCollection($collection);

        $url = RouteHelper::url('dashboard');
        $this->assertSame('/dashboard', $url);
    }

    public function testRouteHelperSubstitutesParameters(): void
    {
        $collection = new RouteCollection();
        $collection->add(new RouteDefinition('/users/{id}', 'GET', 'Ctrl', 'show', 'users.show'));

        RouteHelper::setRouteCollection($collection);

        $url = RouteHelper::url('users.show', ['id' => 42]);
        $this->assertSame('/users/42', $url);
    }

    public function testRouteHelperMultipleParameters(): void
    {
        $collection = new RouteCollection();
        $collection->add(new RouteDefinition(
            '/users/{id}/posts/{slug}', 'GET', 'Ctrl', 'show', 'users.posts.show'
        ));

        RouteHelper::setRouteCollection($collection);

        $url = RouteHelper::url('users.posts.show', ['id' => 5, 'slug' => 'hello']);
        $this->assertSame('/users/5/posts/hello', $url);
    }

    public function testRouteHelperExtraParamsAsQueryString(): void
    {
        $collection = new RouteCollection();
        $collection->add(new RouteDefinition('/users', 'GET', 'Ctrl', 'index', 'users.index'));

        RouteHelper::setRouteCollection($collection);

        $url = RouteHelper::url('users.index', ['page' => 2, 'sort' => 'name']);
        $this->assertSame('/users?page=2&sort=name', $url);
    }

    public function testRouteHelperThrowsOnMissingRoute(): void
    {
        $collection = new RouteCollection();
        RouteHelper::setRouteCollection($collection);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("not found");
        RouteHelper::url('nonexistent');
    }

    public function testRouteHelperThrowsOnMissingParameter(): void
    {
        $collection = new RouteCollection();
        $collection->add(new RouteDefinition('/users/{id}', 'GET', 'Ctrl', 'show', 'users.show'));

        RouteHelper::setRouteCollection($collection);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("missing parameter 'id'");
        RouteHelper::url('users.show');
    }

    public function testRouteHelperThrowsWithoutCollection(): void
    {
        // Reset static state
        $reflection = new ReflectionClass(RouteHelper::class);
        $prop = $reflection->getProperty('collection');
        $prop->setAccessible(true);
        $prop->setValue(null, null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("RouteCollection not set");
        RouteHelper::url('test');
    }
}
