<?php

declare(strict_types=1);

namespace Fixtures;

use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Post;
use App\Core\Http\Attributes\Put;
use App\Core\Http\Attributes\Patch;
use App\Core\Http\Attributes\Delete;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\NamePrefix;
use App\Core\Http\Attributes\RouteAttr;

// --- Inheritance fixtures ---

#[Prefix('/api')]
#[Middleware('auth')]
class BaseApiController
{
    #[Get('/parent-route', name: 'parent.route')]
    public function parentAction(): void {}
}

#[Prefix('/admin')]
#[Middleware('admin')]
class ChildAdminController extends BaseApiController
{
    #[Get('/dashboard', name: 'admin.dashboard')]
    public function dashboard(): void {}
}

// Child that inherits everything without own class attributes
class BareChildController extends BaseApiController
{
    #[Get('/child-only')]
    public function childAction(): void {}
}

// NamePrefix inheritance: child overrides parent
#[NamePrefix('parent.')]
class ParentNamePrefixController
{
    #[Get('/test')]
    public function test(): void {}
}

#[NamePrefix('child.')]
class ChildNamePrefixController extends ParentNamePrefixController
{
    #[Get('/sub')]
    public function sub(): void {}
}

// Duplicate middleware in hierarchy
#[Middleware('auth')]
class ParentDupMiddleware
{
    #[Get('/dup')]
    public function dup(): void {}
}

#[Middleware('auth')]
class DuplicateMiddlewareController extends ParentDupMiddleware
{
    #[Get('/child-dup')]
    public function childDup(): void {}
}

// --- Mixed Spatie + legacy ---

class MixedAttributeController
{
    #[Get('/mixed/spatie', name: 'mixed.spatie')]
    public function spatieMethod(): void {}

    #[RouteAttr('/mixed/legacy', 'POST', 'mixed.legacy')]
    public function legacyMethod(): void {}
}

// --- All HTTP methods ---

class AllMethodsController
{
    #[Get('/resources')]
    public function index(): void {}

    #[Post('/resources')]
    public function store(): void {}

    #[Put('/resources/{id}')]
    public function update(): void {}

    #[Patch('/resources/{id}/partial')]
    public function partialUpdate(): void {}

    #[Delete('/resources/{id}')]
    public function destroy(): void {}
}

// --- Prefix + route ---

#[Prefix('/admin')]
class PrefixedController
{
    #[Get('/users', name: 'admin.users')]
    public function index(): void {}

    #[Post('/users/create', name: 'admin.users.create')]
    public function create(): void {}
}

// Nested prefix through inheritance
#[Prefix('/v1')]
class NestedPrefixChildController extends BaseApiController
{
    #[Get('/items')]
    public function items(): void {}
}

// --- NamePrefix ---

#[NamePrefix('admin.')]
class NamePrefixedController
{
    #[Get('/users', name: 'users.index')]
    public function index(): void {}

    #[Post('/users', name: 'users.store')]
    public function store(): void {}
}

#[NamePrefix('ignored.')]
class NamePrefixNoNameController
{
    #[Get('/no-name')]
    public function noName(): void {}
}

// --- Middleware merge controller+route ---

#[Middleware('auth')]
class MiddlewareMergeController
{
    #[Get('/protected', middleware: ['throttle'])]
    public function protectedAction(): void {}
}

// --- Helper method (public, no attribute) ---

class HelperMethodController
{
    #[Get('/index')]
    public function index(): void {}

    public function helperThatShouldBeSkipped(): string
    {
        return 'helper';
    }
}

// --- Controller with constructor ---

class ControllerWithConstructor
{
    public function __construct() {}

    #[Get('/index')]
    public function index(): void {}
}

class InvalidSpatieRouteController
{
    #[Get(123)]
    public function broken(): void {}
}
