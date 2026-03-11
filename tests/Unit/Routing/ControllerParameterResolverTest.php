<?php

declare(strict_types=1);

use App\Core\Http\ControllerParameterResolver;
use App\Core\Http\Helpers\RouteDefinition;
use App\Core\Http\Request;
use PHPUnit\Framework\TestCase;

class ControllerParameterResolverFixtureParameterController
{
    public function show(int $id): void
    {
    }
}

class ControllerParameterResolverFixtureRequestAwareController
{
    public function show(Request $request, int $id): void
    {
    }
}

class ControllerParameterResolverFixtureDefaultValueController
{
    public function index(string $status = 'published'): void
    {
    }
}

class ControllerParameterResolverFixtureLooseNamesController
{
    public function show(int $projectId, string $projectSlug): void
    {
    }
}

class ControllerParameterResolverFixtureMixedOrderController
{
    public function show(Request $request, int $projectId, string $projectSlug = 'fallback'): void
    {
    }
}

class ControllerParameterResolverTest extends TestCase
{
    private ControllerParameterResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new ControllerParameterResolver();
    }

    public function testResolvesRouteParametersByNameAndCastsBuiltinTypes(): void
    {
        $route = new RouteDefinition('/users/{id}', 'GET', ControllerParameterResolverFixtureParameterController::class, 'show');
        $route->setParam(['id' => '42']);

        $reflection = new ReflectionMethod(ControllerParameterResolverFixtureParameterController::class, 'show');
        $args = $this->resolver->resolve($reflection, $route);

        $this->assertSame([42], $args);
    }

    public function testResolvesInjectedRequestParameter(): void
    {
        $_SERVER['REQUEST_URI'] = '/users/42';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $request = new Request();

        $route = new RouteDefinition('/users/{id}', 'GET', ControllerParameterResolverFixtureRequestAwareController::class, 'show');
        $route->setParam(['id' => '42']);

        $reflection = new ReflectionMethod(ControllerParameterResolverFixtureRequestAwareController::class, 'show');
        $args = $this->resolver->resolve($reflection, $route, $request);

        $this->assertSame($request, $args[0]);
        $this->assertSame(42, $args[1]);
    }

    public function testUsesDefaultValuesWhenRouteParameterIsMissing(): void
    {
        $route = new RouteDefinition('/users', 'GET', ControllerParameterResolverFixtureDefaultValueController::class, 'index');
        $route->setParam([]);

        $reflection = new ReflectionMethod(ControllerParameterResolverFixtureDefaultValueController::class, 'index');
        $args = $this->resolver->resolve($reflection, $route);

        $this->assertSame(['published'], $args);
    }

    public function testThrowsHelpfulErrorWhenRequiredRequestIsMissing(): void
    {
        $route = new RouteDefinition('/users/{id}', 'GET', ControllerParameterResolverFixtureRequestAwareController::class, 'show');
        $route->setParam(['id' => '42']);

        $reflection = new ReflectionMethod(ControllerParameterResolverFixtureRequestAwareController::class, 'show');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to resolve Request');

        $this->resolver->resolve($reflection, $route);
    }

    public function testResolvesRemainingRouteParametersWhenControllerNamesDiffer(): void
    {
        $route = new RouteDefinition(
            '/projects/{id}/{slug}',
            'GET',
            ControllerParameterResolverFixtureLooseNamesController::class,
            'show'
        );
        $route->setParam(['id' => '12', 'slug' => 'demo-project']);

        $reflection = new ReflectionMethod(ControllerParameterResolverFixtureLooseNamesController::class, 'show');
        $args = $this->resolver->resolve($reflection, $route);

        $this->assertSame([12, 'demo-project'], $args);
    }

    public function testResolvesMixedRequestAndFallbackRouteParametersWithoutStrictOrder(): void
    {
        $_SERVER['REQUEST_URI'] = '/projects/12/demo-project';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $request = new Request();

        $route = new RouteDefinition(
            '/projects/{id}/{slug}',
            'GET',
            ControllerParameterResolverFixtureMixedOrderController::class,
            'show'
        );
        $route->setParam(['id' => '12', 'slug' => 'demo-project']);

        $reflection = new ReflectionMethod(ControllerParameterResolverFixtureMixedOrderController::class, 'show');
        $args = $this->resolver->resolve($reflection, $route, $request);

        $this->assertSame($request, $args[0]);
        $this->assertSame(12, $args[1]);
        $this->assertSame('demo-project', $args[2]);
    }
}
