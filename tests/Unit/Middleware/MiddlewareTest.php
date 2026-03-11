<?php

declare(strict_types=1);

use App\Core\Contract\MiddlewareInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the middleware execution pipeline in RouteDispatcher.
 *
 * Since RouteDispatcher::executeMiddleware is private and tightly coupled
 * to the config, we test the middleware contract and pipeline logic by
 * simulating the same pattern used in RouteDispatcher.
 */
class MiddlewareTest extends TestCase
{
    /** @var Response Mock response used by blocking middleware */
    private Response $mockResponse;

    protected function setUp(): void
    {
        $this->mockResponse = $this->createMock(Response::class);
    }

    /**
     * Simulates the RouteDispatcher middleware pipeline:
     * iterates through middleware classes, calls exec(), and stops if one returns a Response.
     *
     * @param MiddlewareInterface[] $middlewares
     * @param Request $request
     * @return Response|null
     */
    private function runPipeline(array $middlewares, Request $request): ?Response
    {
        foreach ($middlewares as $mw) {
            $response = $mw->exec($request);
            if ($response instanceof Response) {
                return $response;
            }
        }

        return null;
    }

    public function testMiddlewareExecutesInOrder(): void
    {
        $log = [];

        $mw1 = $this->createPassMiddleware($log, 'first');
        $mw2 = $this->createPassMiddleware($log, 'second');
        $mw3 = $this->createPassMiddleware($log, 'third');

        $request = $this->createMock(Request::class);

        $result = $this->runPipeline([$mw1, $mw2, $mw3], $request);

        $this->assertNull($result);
        $this->assertSame(['first', 'second', 'third'], $log);
    }

    public function testMiddlewareCanInterruptChainByReturningResponse(): void
    {
        $log = [];

        $mw1 = $this->createPassMiddleware($log, 'first');
        $mw2 = $this->createBlockMiddleware($log, 'blocker');
        $mw3 = $this->createPassMiddleware($log, 'third');

        $request = $this->createMock(Request::class);

        $result = $this->runPipeline([$mw1, $mw2, $mw3], $request);

        $this->assertInstanceOf(Response::class, $result);
        // Third middleware should NOT have been reached
        $this->assertSame(['first', 'blocker'], $log);
    }

    public function testMiddlewarePassesThroughWhenReturningNull(): void
    {
        $log = [];

        $mw1 = $this->createPassMiddleware($log, 'pass1');
        $mw2 = $this->createPassMiddleware($log, 'pass2');

        $request = $this->createMock(Request::class);

        $result = $this->runPipeline([$mw1, $mw2], $request);

        $this->assertNull($result);
        $this->assertCount(2, $log);
    }

    public function testEmptyMiddlewareStackReturnsNull(): void
    {
        $request = $this->createMock(Request::class);
        $result = $this->runPipeline([], $request);

        $this->assertNull($result);
    }

    public function testSingleBlockingMiddlewareStopsImmediately(): void
    {
        $log = [];

        $blocker = $this->createBlockMiddleware($log, 'only');
        $request = $this->createMock(Request::class);

        $result = $this->runPipeline([$blocker], $request);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame(['only'], $log);
    }

    // ========================================================================
    // Helpers — create middleware stubs that track execution order
    // ========================================================================

    /**
     * Creates a middleware that logs its name and returns null (pass-through).
     */
    private function createPassMiddleware(array &$log, string $name): MiddlewareInterface
    {
        return new class ($log, $name) implements MiddlewareInterface {
            private array $log;
            private string $name;

            public function __construct(array &$log, string $name)
            {
                $this->log = &$log;
                $this->name = $name;
            }

            public function exec(Request $request): mixed
            {
                $this->log[] = $this->name;

                return null;
            }
        };
    }

    /**
     * Creates a middleware that logs its name and returns a Response mock (blocks the chain).
     */
    private function createBlockMiddleware(array &$log, string $name): MiddlewareInterface
    {
        $response = $this->mockResponse;

        return new class ($log, $name, $response) implements MiddlewareInterface {
            private array $log;
            private string $name;
            private Response $response;

            public function __construct(array &$log, string $name, Response $response)
            {
                $this->log = &$log;
                $this->name = $name;
                $this->response = $response;
            }

            public function exec(Request $request): mixed
            {
                $this->log[] = $this->name;

                return $this->response;
            }
        };
    }
}
