<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Delete;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Patch;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Attributes\Post;
use App\Core\Http\Request;
use App\Services\LawService;

#[Prefix('/admin')]
#[Middleware('auth')]
class LawsMngController extends AdminController
{

    #[Get('/laws')]
    public function index()
    {
        $laws = LawService::getAll();
        return view('admin.laws.index', compact('laws'));
    }

    #[Post('/laws')]
    public function store(Request $request)
    {
        LawService::create($request->all());

        return response()->back()->withSuccess('New Law has be created');
    }

    #[Get('laws/{id}', 'laws.edit')]
    public function edit(Request $req, string $id)
    {
        $law = LawService::findOrFail((int) $id);

        $laws = LawService::getAll();

        return view('admin.laws.index', compact('laws', 'law'));
    }

    #[Patch('laws/{id}', 'laws.update')]
    public function update(Request $request, string $id)
    {
        LawService::update((int) $id, $request->all());

        return response()->back()->withSuccess('Law is Updated');
    }

    #[Delete('laws-delete/{id}', 'laws.delete')]
    public function destroy(Request $req, string $id)
    {
        LawService::delete((int) $id);

        return response()->back()->withSuccess("Law DELETE");
    }
}
