<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Delete;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Patch;
use App\Core\Http\Attributes\Post;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Request;
use App\Core\Validation\Validator;
use App\Services\LinkFooterService;

#[Prefix('/admin')]
#[Middleware('auth')]
class LinkFooterMngController extends AdminController
{
    #[Get('/footer-links', 'admin.footer-links')]
    public function index()
    {
        return view('admin.portfolio.footer-links', [
            'links' => LinkFooterService::getAll(),
            'item' => null,
        ]);
    }

    #[Get('/footer-links-edit/{id}', 'admin.footer-links.edit')]
    public function edit(int $id)
    {
        return view('admin.portfolio.footer-links', [
            'links' => LinkFooterService::getAll(),
            'item' => LinkFooterService::findOrFail($id),
        ]);
    }

    #[Post('/footer-links', 'admin.footer-links.store')]
    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        if ($data instanceof Validator) {
            return response()->back()->withError($data->implodeError());
        }

        LinkFooterService::create($data);

        return response()->back()->withSuccess('Link footer creato con successo.');
    }

    #[Patch('/footer-links-update/{id}', 'admin.footer-links.update')]
    public function update(Request $request, int $id)
    {
        $data = $this->validatePayload($request);

        if ($data instanceof Validator) {
            return response()->back()->withError($data->implodeError());
        }

        LinkFooterService::update($id, $data);

        return response()->back()->withSuccess('Link footer aggiornato con successo.');
    }

    #[Delete('/footer-links-delete/{id}', 'admin.footer-links.delete')]
    public function destroy(int $id)
    {
        LinkFooterService::delete($id);

        return response()->back()->withSuccess('Link footer eliminato con successo.');
    }

    /**
     * @return array{title: string, link: string}|Validator
     */
    private function validatePayload(Request $request): array|Validator
    {
        $data = [
            'title' => trim($request->string('title')),
            'link' => trim($request->string('link')),
        ];

        $validator = Validator::make($data, [
            'title' => ['required', 'string', 'max:50'],
            'link' => ['required', 'string', 'max:255'],
        ], [
            'title.required' => 'Il titolo del link e obbligatorio.',
            'link.required' => 'Il link del footer e obbligatorio.',
        ]);

        if ($validator->fails()) {
            return $validator;
        }

        return $data;
    }
}
