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
use App\Services\PartnerService;

#[Prefix('/admin')]
#[Middleware('auth')]
class PartnerMngController extends AdminController
{
    #[Get('/partner', 'admin.partner')]
    public function index()
    {
        return view('admin.portfolio.partner', [
            'partners' => PartnerService::getAll(),
            'partner' => null,
        ]);
    }

    #[Get('/partner-edit/{id}', 'admin.partner.edit')]
    public function edit(int $id)
    {
        return view('admin.portfolio.partner', [
            'partners' => PartnerService::getAll(),
            'partner' => PartnerService::findOrFail($id),
        ]);
    }

    #[Post('/partner', 'admin.partner.store')]
    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        if ($data instanceof Validator) {
            return response()->back()->withError($data->implodeError());
        }

        PartnerService::create($data);

        return response()->back()->withSuccess('Partner creato con successo.');
    }

    #[Patch('/partner-update/{id}', 'admin.partner.update')]
    public function update(Request $request, int $id)
    {
        $data = $this->validatePayload($request);

        if ($data instanceof Validator) {
            return response()->back()->withError($data->implodeError());
        }

        PartnerService::update($id, $data);

        return response()->back()->withSuccess('Partner aggiornato con successo.');
    }

    #[Delete('/partner-delete/{id}', 'admin.partner.delete')]
    public function destroy(int $id)
    {
        PartnerService::delete($id);

        return response()->back()->withSuccess('Partner eliminato con successo.');
    }

    /**
     * @return array{name: string, website: ?string}|Validator
     */
    private function validatePayload(Request $request): array|Validator
    {
        $website = trim($request->string('website'));
        $data = [
            'name' => trim($request->string('name')),
            'website' => $website !== '' ? $website : null,
        ];

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:30'],
            'website' => ['nullable', 'url'],
        ], [
            'name.required' => 'Il nome del partner e obbligatorio.',
            'name.string' => 'Il nome del partner deve essere una stringa.',
            'name.max' => 'Il nome del partner non puo superare 30 caratteri.',
            'website.url' => 'Il sito del partner deve essere un URL valido.',
        ]);

        if ($validator->fails()) {
            return $validator;
        }

        return $data;
    }
}
