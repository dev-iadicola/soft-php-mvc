<?php

declare(strict_types=1);

use App\Core\Http\Helpers\RouteCollection;
use App\Core\Http\Helpers\RouteDefinition;
use App\Core\Http\Helpers\Stack;
use PHPUnit\Framework\TestCase;

class RouteCollectionTest extends TestCase
{
    private function makeRoute(
        string $uri = '/test',
        string $method = 'GET',
        string $controller = 'TestController',
        string $action = 'index',
        ?string $name = null,
        array $middleware = [],
    ): RouteDefinition {
        return new RouteDefinition($uri, $method, $controller, $action, $name, $middleware);
    }

    public function testAddAndRetrieveRoutes(): void
    {
        $collection = new RouteCollection();
        $route = $this->makeRoute('/home', 'GET', 'HomeCtrl', 'index');
        $collection->add($route);

        $all = $collection->all();
        $this->assertCount(1, $all);
        $this->assertSame('/home', $all[0]->uri);
        $this->assertSame('GET', $all[0]->method);
    }

    public function testNamedRouteRegistration(): void
    {
        $collection = new RouteCollection();
        $route = $this->makeRoute('/dashboard', 'GET', 'DashCtrl', 'index', 'admin.dashboard');
        $collection->add($route);

        $found = $collection->findByName('admin.dashboard');
        $this->assertNotNull($found);
        $this->assertSame('/dashboard', $found->uri);
    }

    public function testFindByNameReturnsNullForMissing(): void
    {
        $collection = new RouteCollection();
        $this->assertNull($collection->findByName('nonexistent'));
    }

    public function testFilterByMethod(): void
    {
        $collection = new RouteCollection();
        $collection->add($this->makeRoute('/a', 'GET', 'C', 'a'));
        $collection->add($this->makeRoute('/b', 'POST', 'C', 'b'));
        $collection->add($this->makeRoute('/c', 'GET', 'C', 'c'));

        $getRoutes = $collection->filter('GET');
        $this->assertCount(2, $getRoutes->all());

        $postRoutes = $collection->filter('POST');
        $this->assertCount(1, $postRoutes->all());

        $putRoutes = $collection->filter('PUT');
        $this->assertCount(0, $putRoutes->all());
    }

    public function testDuplicateRouteThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Duplicate route');

        $collection = new RouteCollection();
        $collection->add($this->makeRoute('/same', 'GET', 'CtrlA', 'index'));
        $collection->add($this->makeRoute('/same', 'GET', 'CtrlB', 'show'));
    }

    public function testSamePathDifferentMethodsAllowed(): void
    {
        $collection = new RouteCollection();
        $collection->add($this->makeRoute('/resource', 'GET', 'Ctrl', 'index'));
        $collection->add($this->makeRoute('/resource', 'POST', 'Ctrl', 'store'));

        $this->assertCount(2, $collection->all());
    }

    public function testInvalidHttpMethodThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("not allowed");

        $collection = new RouteCollection();
        $collection->add($this->makeRoute('/bad', 'INVALID', 'Ctrl', 'action'));
    }

    public function testIteratorInterface(): void
    {
        $collection = new RouteCollection();
        $collection->add($this->makeRoute('/one', 'GET', 'C', 'one'));
        $collection->add($this->makeRoute('/two', 'POST', 'C', 'two'));

        $count = 0;
        foreach ($collection as $route) {
            $this->assertInstanceOf(RouteDefinition::class, $route);
            $count++;
        }
        $this->assertSame(2, $count);
    }

    public function testStackMiddlewareAndPath(): void
    {
        $stack = new Stack();
        $stack->addMiddleware('auth');
        $stack->addMiddleware(['admin', 'web']);
        $stack->addPath('/admin');

        $stack->clean();

        $this->assertSame(['auth', 'admin', 'web'], $stack->Middleware()->toArray());
        $this->assertSame(['/admin'], $stack->Path()->toArray());
    }

    public function testStackNamePrefix(): void
    {
        $stack = new Stack();
        $stack->setNamePrefix('admin.');

        $this->assertSame('admin.', $stack->getNamePrefix());
    }

    public function testStackMerge(): void
    {
        $stack1 = new Stack();
        $stack1->addMiddleware('web');
        $stack1->addPath('/api');

        $stack2 = new Stack();
        $stack2->addMiddleware('auth');
        $stack2->addPath('/v1');

        $stack1->merge($stack2);

        $this->assertContains('web', $stack1->Middleware()->toArray());
        $this->assertContains('auth', $stack1->Middleware()->toArray());
        $this->assertContains('/api', $stack1->Path()->toArray());
        $this->assertContains('/v1', $stack1->Path()->toArray());
    }

    public function testStackCleanRemovesDuplicates(): void
    {
        $stack = new Stack();
        $stack->addMiddleware(['auth', 'auth', 'web']);
        $stack->clean();

        $mw = $stack->Middleware()->toArray();
        $this->assertSame(['auth', 'web'], $mw);
    }
}
