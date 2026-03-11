<?php

declare(strict_types=1);

use App\Core\Http\Helpers\RouteCollection;
use App\Core\Http\Helpers\RouteDefinition;
use App\Core\Http\Request;
use App\Core\Http\RouteMatcher;
use PHPUnit\Framework\TestCase;

class RouteMatcherTest extends TestCase
{
    private RouteMatcher $matcher;

    protected function setUp(): void
    {
        $this->matcher = new RouteMatcher();
    }

    private function makeCollection(array $routes): RouteCollection
    {
        $collection = new RouteCollection();
        foreach ($routes as $route) {
            $collection->add($route);
        }
        return $collection;
    }

    private function makeRoute(
        string $uri,
        string $method = 'GET',
        string $controller = 'TestController',
        string $action = 'index',
    ): RouteDefinition {
        return new RouteDefinition($uri, $method, $controller, $action);
    }

    private function makeRequest(string $uri, string $method = 'GET'): Request
    {
        // Crea un mock di Request per i test
        $request = $this->createMock(Request::class);
        $request->method('uri')->willReturn($uri);
        $request->method('getRequestMethod')->willReturn($method);
        return $request;
    }

    public function testExactPathMatch(): void
    {
        $collection = $this->makeCollection([
            $this->makeRoute('/home'),
            $this->makeRoute('/about'),
        ]);

        $request = $this->makeRequest('/home');
        $result = $this->matcher->match($request, $collection);

        $this->assertNotNull($result);
        $this->assertSame('/home', $result->uri);
    }

    public function testNoMatchReturnsNull(): void
    {
        $collection = $this->makeCollection([
            $this->makeRoute('/home'),
        ]);

        $request = $this->makeRequest('/nonexistent');
        $result = $this->matcher->match($request, $collection);

        $this->assertNull($result);
    }

    public function testParameterCapture(): void
    {
        $collection = $this->makeCollection([
            $this->makeRoute('/users/{id}'),
        ]);

        $request = $this->makeRequest('/users/42');
        $result = $this->matcher->match($request, $collection);

        $this->assertNotNull($result);
        $this->assertSame('42', $result->getParam('id'));
    }

    public function testMultipleParameterCapture(): void
    {
        $collection = $this->makeCollection([
            $this->makeRoute('/users/{id}/posts/{slug}'),
        ]);

        $request = $this->makeRequest('/users/5/posts/hello-world');
        $result = $this->matcher->match($request, $collection);

        $this->assertNotNull($result);
        $this->assertSame('5', $result->getParam('id'));
        $this->assertSame('hello-world', $result->getParam('slug'));
    }

    public function testMethodFiltering(): void
    {
        $collection = $this->makeCollection([
            $this->makeRoute('/login', 'GET', 'AuthCtrl', 'showForm'),
            $this->makeRoute('/login', 'POST', 'AuthCtrl', 'login'),
        ]);

        $getRequest = $this->makeRequest('/login', 'GET');
        $getResult = $this->matcher->match($getRequest, $collection);
        $this->assertNotNull($getResult);
        $this->assertSame('showForm', $getResult->action);

        $postRequest = $this->makeRequest('/login', 'POST');
        $postResult = $this->matcher->match($postRequest, $collection);
        $this->assertNotNull($postResult);
        $this->assertSame('login', $postResult->action);
    }

    public function testStaticRouteMatchedBeforeParameterized(): void
    {
        $collection = $this->makeCollection([
            $this->makeRoute('/users/create', 'GET', 'UserCtrl', 'create'),
            $this->makeRoute('/users/{id}', 'GET', 'UserCtrl', 'show'),
        ]);

        $request = $this->makeRequest('/users/create');
        $result = $this->matcher->match($request, $collection);

        $this->assertNotNull($result);
        $this->assertSame('create', $result->action);
    }

    public function testDoesNotMatchPartialPaths(): void
    {
        $collection = $this->makeCollection([
            $this->makeRoute('/users'),
        ]);

        // /users/extra non deve corrispondere a /users
        $request = $this->makeRequest('/users/extra');
        $result = $this->matcher->match($request, $collection);

        $this->assertNull($result);
    }

    public function testRootPath(): void
    {
        $collection = $this->makeCollection([
            $this->makeRoute('/'),
        ]);

        $request = $this->makeRequest('/');
        $result = $this->matcher->match($request, $collection);

        $this->assertNotNull($result);
        $this->assertSame('/', $result->uri);
    }

    public function testWrongMethodReturnsNull(): void
    {
        $collection = $this->makeCollection([
            $this->makeRoute('/resource', 'POST'),
        ]);

        $request = $this->makeRequest('/resource', 'GET');
        $result = $this->matcher->match($request, $collection);

        $this->assertNull($result);
    }
}
