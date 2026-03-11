<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;
use App\Services\LawService;

class LawsMngController extends AdminController
{

    #[RouteAttr('/laws')]
    public function index()
    {
        $laws = LawService::getAll();
        return view('admin.laws.index', compact('laws'));
    }

    #[RouteAttr('/laws', 'POST')]
    public function store(Request $request)
    {
        LawService::create($request->all());

        $this->withSuccess('New Law has be created');
        return response()->back();
    }

    #[RouteAttr(path: 'laws/{id}', method: 'get', name: 'laws.edit')]
    public function edit(Request $req, string $id)
    {
        $law = LawService::findOrFail((int) $id);

        $laws = LawService::getAll();

        return view('admin.laws.index', compact('laws', 'law'));
    }

    #[RouteAttr(path: 'laws/{id}', method: 'patch', name: 'laws.update')]
    public function update(Request $request, string $id)
    {
        LawService::update((int) $id, $request->all());

        $this->withSuccess('Law is Updated');
        return response()->back();
    }

    #[RouteAttr(path: 'laws-delete/{id}', method: 'DELETE', name: 'laws.delete')]
    public function destroy(Request $req, string $id)
    {
        LawService::delete((int) $id);

        return response()->back()->withSuccess("Law DELETE");
    }
}
