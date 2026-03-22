<?php

declare(strict_types=1);

use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Post;
use App\Core\Http\Attributes\Put;
use App\Core\Http\Attributes\Patch;
use App\Core\Http\Attributes\Delete;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\NamePrefix;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Attributes\RouteAttribute;
use PHPUnit\Framework\TestCase;

class RouteAttributeTest extends TestCase
{
    // ========================================================================
    // #[Get] attribute
    // ========================================================================

    public function testGetAttributeCreatesGetRoute(): void
    {
        $attr = new Get('/users');
        $this->assertSame('GET', $attr->method);
        $this->assertSame('/users', $attr->path);
    }

    public function testGetAttributeWithNameAndMiddleware(): void
    {
        $attr = new Get('/users', name: 'users.index', middleware: ['auth']);
        $this->assertSame('users.index', $attr->name);
        $this->assertContains('auth', $attr->middleware);
        $this->assertContains('web', $attr->middleware);
    }

    // ========================================================================
    // #[Post] attribute
    // ========================================================================

    public function testPostAttributeCreatesPostRoute(): void
    {
        $attr = new Post('/users');
        $this->assertSame('POST', $attr->method);
        $this->assertSame('/users', $attr->path);
    }

    public function testPostAttributeWithName(): void
    {
        $attr = new Post('/users', name: 'users.store');
        $this->assertSame('users.store', $attr->name);
    }

    // ========================================================================
    // #[Put] attribute
    // ========================================================================

    public function testPutAttributeCreatesPutRoute(): void
    {
        $attr = new Put('/users/{id}');
        $this->assertSame('PUT', $attr->method);
        $this->assertSame('/users/{id}', $attr->path);
    }

    // ========================================================================
    // #[Patch] attribute
    // ========================================================================

    public function testPatchAttributeCreatesPatchRoute(): void
    {
        $attr = new Patch('/users/{id}');
        $this->assertSame('PATCH', $attr->method);
    }

    // ========================================================================
    // #[Delete] attribute
    // ========================================================================

    public function testDeleteAttributeCreatesDeleteRoute(): void
    {
        $attr = new Delete('/users/{id}');
        $this->assertSame('DELETE', $attr->method);
    }

    // ========================================================================
    // #[Prefix] class attribute
    // ========================================================================

    public function testPrefixAttributeAddsSlashPrefix(): void
    {
        $prefix = new Prefix('/admin');
        $this->assertSame('/admin', $prefix->prefix);
    }

    public function testPrefixAttributeNormalizesPathWithoutLeadingSlash(): void
    {
        $prefix = new Prefix('api');
        $this->assertSame('/api', $prefix->prefix);
    }

    public function testPrefixAttributeTrimsWhitespace(): void
    {
        $prefix = new Prefix('  /admin  ');
        $this->assertSame('/admin', $prefix->prefix);
    }

    // ========================================================================
    // #[Middleware] class attribute
    // ========================================================================

    public function testMiddlewareAttributeAcceptsArray(): void
    {
        $mw = new Middleware(['auth', 'admin']);
        $this->assertSame(['auth', 'admin'], $mw->middleware);
    }

    public function testMiddlewareAttributeAcceptsString(): void
    {
        $mw = new Middleware('auth');
        $this->assertSame(['auth'], $mw->middleware);
    }

    // ========================================================================
    // #[NamePrefix] class attribute
    // ========================================================================

    public function testNamePrefixAttributeStoresPrefix(): void
    {
        $np = new NamePrefix('admin.');
        $this->assertSame('admin.', $np->namePrefix);
    }

    public function testNamePrefixAttributeWithEmptyString(): void
    {
        $np = new NamePrefix('');
        $this->assertSame('', $np->namePrefix);
    }

    // ========================================================================
    // Path normalization (RouteAttribute base)
    // ========================================================================

    public function testRouteAttributeNormalizesPathWithoutLeadingSlash(): void
    {
        $attr = new Get('users');
        $this->assertSame('/users', $attr->path);
    }

    public function testRouteAttributeConvertsDotNotationToSlashes(): void
    {
        $attr = new Get('admin.users.list');
        $this->assertSame('/admin/users/list', $attr->path);
    }

    public function testRouteAttributeTrimsWhitespace(): void
    {
        $attr = new Get('  /users  ');
        $this->assertSame('/users', $attr->path);
    }

    // ========================================================================
    // Web middleware auto-injection
    // ========================================================================

    public function testRouteAttributeAddsWebMiddlewareByDefault(): void
    {
        $attr = new Get('/test');
        $this->assertContains('web', $attr->middleware);
    }

    public function testRouteAttributeDoesNotDuplicateWebMiddleware(): void
    {
        $attr = new Get('/test', middleware: ['web', 'auth']);
        $count = array_count_values($attr->middleware);
        $this->assertSame(1, $count['web']);
    }

    public function testRouteAttributePreservesCustomMiddlewareOrder(): void
    {
        $attr = new Get('/test', middleware: ['auth', 'admin']);
        // 'web' is prepended
        $this->assertSame('web', $attr->middleware[0]);
        $this->assertContains('auth', $attr->middleware);
        $this->assertContains('admin', $attr->middleware);
    }

    // ========================================================================
    // Backward compatibility with legacy #[RouteAttr]
    // ========================================================================

    public function testLegacyRouteAttrCreatesCorrectRoute(): void
    {
        $attr = new RouteAttr('/dashboard', 'GET', 'admin.dashboard');
        $this->assertSame('/dashboard', $attr->path);
        $this->assertSame('GET', $attr->method);
        $this->assertSame('admin.dashboard', $attr->name);
        $this->assertContains('web', $attr->middleware);
    }

    public function testLegacyRouteAttrWithPostMethod(): void
    {
        $attr = new RouteAttr('/login', 'POST');
        $this->assertSame('POST', $attr->method);
    }

    public function testLegacyRouteAttrNormalizesDotPath(): void
    {
        $attr = new RouteAttr('admin.dashboard');
        $this->assertSame('/admin/dashboard', $attr->path);
    }

    public function testLegacyRouteAttrAddsWebMiddleware(): void
    {
        $attr = new RouteAttr('/test');
        $this->assertContains('web', $attr->middleware);
    }

    // ========================================================================
    // RouteAttribute is parent of Get, Post, etc.
    // ========================================================================

    public function testGetExtendsRouteAttribute(): void
    {
        $attr = new Get('/test');
        $this->assertInstanceOf(RouteAttribute::class, $attr);
    }

    public function testPostExtendsRouteAttribute(): void
    {
        $attr = new Post('/test');
        $this->assertInstanceOf(RouteAttribute::class, $attr);
    }
}
