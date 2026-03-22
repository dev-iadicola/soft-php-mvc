<?php

declare(strict_types=1);

use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Attributes\RouteAttribute;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Post;
use App\Core\Http\Attributes\Put;
use App\Core\Http\Attributes\Patch;
use App\Core\Http\Attributes\Delete;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\NamePrefix;
use App\Core\Http\Attributes\ControllerAttr;
use App\Core\Http\Helpers\ClassControllers;
use App\Core\Http\Helpers\RouteCollection;
use App\Core\Http\Helpers\RouteDefinition;
use App\Core\Http\Helpers\RouteHelper;
use App\Core\Http\Helpers\Stack;
use App\Core\Http\RouteLoader;
use App\Core\Exception\LoaderAttributeException;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/Fixtures/Controllers.php';

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

    // ========================================================================
    // Helper: invoke private extractRoutes via reflection
    // ========================================================================

    private function invokeExtractRoutes(RouteLoader $loader, ClassControllers $controllers): RouteCollection
    {
        $ref = new ReflectionClass($loader);
        $method = $ref->getMethod('extractRoutes');
        $method->setAccessible(true);
        return $method->invoke($loader, $controllers);
    }

    private function buildStackForClass(string $className): Stack
    {
        $loader = new RouteLoader([]);
        $classControllers = new ClassControllers();
        $classControllers->addController($className);
        $loader->getControllerStacks($classControllers);
        return $classControllers->find($className);
    }

    // ========================================================================
    // 1. Route loading with inheritance — parent class attributes inherited
    // ========================================================================

    public function testControllerInheritsMiddlewareFromParent(): void
    {
        $stack = $this->buildStackForClass(Fixtures\ChildAdminController::class);

        $mw = $stack->Middleware()->toArray();
        // Parent has 'auth', child has 'admin' — both should be present
        $this->assertContains('auth', $mw);
        $this->assertContains('admin', $mw);
    }

    public function testControllerInheritsPrefixFromParent(): void
    {
        $stack = $this->buildStackForClass(Fixtures\ChildAdminController::class);

        $paths = $stack->Path()->toArray();
        // Parent has '/api', child has '/admin'
        $this->assertContains('/api', $paths);
        $this->assertContains('/admin', $paths);
    }

    public function testControllerInheritsNamePrefixFromParent(): void
    {
        // NamePrefix is set (not merged), so child overwrites parent
        $stack = $this->buildStackForClass(Fixtures\ChildNamePrefixController::class);
        // Child sets 'child.' which should overwrite parent's 'parent.'
        $this->assertSame('child.', $stack->getNamePrefix());
    }

    public function testControllerWithoutOwnAttributesInheritsParent(): void
    {
        $stack = $this->buildStackForClass(Fixtures\BareChildController::class);

        $mw = $stack->Middleware()->toArray();
        $this->assertContains('auth', $mw);

        $paths = $stack->Path()->toArray();
        $this->assertContains('/api', $paths);
    }

    // ========================================================================
    // 2. Stack middleware merge — correct deduplication
    // ========================================================================

    public function testInheritedMiddlewareDeduplicates(): void
    {
        $stack = $this->buildStackForClass(Fixtures\DuplicateMiddlewareController::class);

        $mw = $stack->Middleware()->toArray();
        // Both parent and child have 'auth', should appear only once
        $authCount = array_count_values($mw)['auth'] ?? 0;
        $this->assertSame(1, $authCount);
    }

    // ========================================================================
    // 3. Spatie-style attributes alongside legacy #[RouteAttr]
    // ========================================================================

    public function testSpatieAndLegacyAttributesCoexist(): void
    {
        $loader = new RouteLoader([]);
        $classControllers = new ClassControllers();
        $classControllers->addController(Fixtures\MixedAttributeController::class);
        $loader->getControllerStacks($classControllers);

        $routes = $this->invokeExtractRoutes($loader, $classControllers);
        $all = $routes->all();

        // Should have 2 routes: one from #[Get] and one from #[RouteAttr]
        $this->assertCount(2, $all);

        $uris = array_map(fn($r) => $r->uri, $all);
        $this->assertContains('/mixed/spatie', $uris);
        $this->assertContains('/mixed/legacy', $uris);
    }

    public function testSpatieGetPostPutPatchDeleteOnSameController(): void
    {
        $loader = new RouteLoader([]);
        $classControllers = new ClassControllers();
        $classControllers->addController(Fixtures\AllMethodsController::class);
        $loader->getControllerStacks($classControllers);

        $routes = $this->invokeExtractRoutes($loader, $classControllers);
        $all = $routes->all();

        $methods = array_map(fn($r) => $r->method, $all);
        $this->assertContains('GET', $methods);
        $this->assertContains('POST', $methods);
        $this->assertContains('PUT', $methods);
        $this->assertContains('PATCH', $methods);
        $this->assertContains('DELETE', $methods);
        $this->assertCount(5, $all);
    }

    // ========================================================================
    // 4. Prefix + route combination
    // ========================================================================

    public function testPrefixCombinesWithRoutePath(): void
    {
        $loader = new RouteLoader([]);
        $classControllers = new ClassControllers();
        $classControllers->addController(Fixtures\PrefixedController::class);
        $loader->getControllerStacks($classControllers);

        $routes = $this->invokeExtractRoutes($loader, $classControllers);
        $all = $routes->all();

        $this->assertCount(2, $all);
        $uris = array_map(fn($r) => $r->uri, $all);
        $this->assertContains('/admin/users', $uris);
        $this->assertContains('/admin/users/create', $uris);
    }

    public function testNestedPrefixFromInheritance(): void
    {
        $loader = new RouteLoader([]);
        $classControllers = new ClassControllers();
        $classControllers->addController(Fixtures\NestedPrefixChildController::class);
        $loader->getControllerStacks($classControllers);

        $routes = $this->invokeExtractRoutes($loader, $classControllers);
        $all = $routes->all();

        $this->assertCount(1, $all);
        // Parent prefix /api + child prefix /v1 + route /items
        $this->assertSame('/api/v1/items', $all[0]->uri);
    }

    // ========================================================================
    // 5. NamePrefix — correctly prefixes route names
    // ========================================================================

    public function testNamePrefixAppliedToRouteNames(): void
    {
        $loader = new RouteLoader([]);
        $classControllers = new ClassControllers();
        $classControllers->addController(Fixtures\NamePrefixedController::class);
        $loader->getControllerStacks($classControllers);

        $routes = $this->invokeExtractRoutes($loader, $classControllers);
        $all = $routes->all();

        $this->assertCount(2, $all);
        $names = array_map(fn($r) => $r->name, $all);
        $this->assertContains('admin.users.index', $names);
        $this->assertContains('admin.users.store', $names);
    }

    public function testNamePrefixNotAppliedWhenRouteHasNoName(): void
    {
        $loader = new RouteLoader([]);
        $classControllers = new ClassControllers();
        $classControllers->addController(Fixtures\NamePrefixNoNameController::class);
        $loader->getControllerStacks($classControllers);

        $routes = $this->invokeExtractRoutes($loader, $classControllers);
        $all = $routes->all();

        $this->assertCount(1, $all);
        // Route has no name, so NamePrefix should not apply
        $this->assertNull($all[0]->name);
    }

    // ========================================================================
    // 6. Error reporting for malformed attributes
    // ========================================================================

    public function testLoaderThrowsOnInvalidControllerDirectory(): void
    {
        $this->expectException(LoaderAttributeException::class);
        $this->expectExceptionMessage("Controller directory not found");

        $loader = new RouteLoader(['App\\Fake' => '/nonexistent/path']);
        $loader->load();
    }

    public function testLoaderReportsMalformedSpatieRouteAttributeWithAttributeName(): void
    {
        $loader = new RouteLoader([]);
        $classControllers = new ClassControllers();
        $classControllers->addController(Fixtures\InvalidSpatieRouteController::class);
        $loader->getControllerStacks($classControllers);

        $this->expectException(LoaderAttributeException::class);
        $this->expectExceptionMessage('Invalid route attribute App\Core\Http\Attributes\Get');
        $this->expectExceptionMessage('InvalidSpatieRouteController::broken()');

        $this->invokeExtractRoutes($loader, $classControllers);
    }

    public function testMiddlewareMergedFromControllerAndRoute(): void
    {
        $loader = new RouteLoader([]);
        $classControllers = new ClassControllers();
        $classControllers->addController(Fixtures\MiddlewareMergeController::class);
        $loader->getControllerStacks($classControllers);

        $routes = $this->invokeExtractRoutes($loader, $classControllers);
        $all = $routes->all();

        $this->assertCount(1, $all);
        $mw = $all[0]->middleware;
        // Controller-level 'auth' + route-level 'web' + route-level 'throttle'
        $this->assertContains('auth', $mw);
        $this->assertContains('web', $mw);
        $this->assertContains('throttle', $mw);
    }

    public function testPublicMethodWithoutRouteAttributeIsSkipped(): void
    {
        $loader = new RouteLoader([]);
        $classControllers = new ClassControllers();
        $classControllers->addController(Fixtures\HelperMethodController::class);
        $loader->getControllerStacks($classControllers);

        $routes = $this->invokeExtractRoutes($loader, $classControllers);
        $all = $routes->all();

        // Only the method with #[Get] should produce a route, not the helper
        $this->assertCount(1, $all);
        $this->assertSame('index', $all[0]->action);
    }

    public function testConstructorIsSkipped(): void
    {
        $loader = new RouteLoader([]);
        $classControllers = new ClassControllers();
        $classControllers->addController(Fixtures\ControllerWithConstructor::class);
        $loader->getControllerStacks($classControllers);

        $routes = $this->invokeExtractRoutes($loader, $classControllers);
        $all = $routes->all();

        $this->assertCount(1, $all);
        $this->assertSame('index', $all[0]->action);
    }

    public function testInheritedMethodsFromParentNotDuplicated(): void
    {
        // extractRoutes skips methods where $method->class !== $reflection->getName()
        // so parent methods should NOT be included in child routes
        $loader = new RouteLoader([]);
        $classControllers = new ClassControllers();
        $classControllers->addController(Fixtures\BareChildController::class);
        $loader->getControllerStacks($classControllers);

        $routes = $this->invokeExtractRoutes($loader, $classControllers);
        $all = $routes->all();

        // BareChildController has only its own childAction, not parent's parentAction
        $actions = array_map(fn($r) => $r->action, $all);
        $this->assertContains('childAction', $actions);
        $this->assertNotContains('parentAction', $actions);
    }
}
