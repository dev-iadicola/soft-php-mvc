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
use App\Services\TechnologyService;

#[Prefix('/admin')]
#[Middleware('auth')]
class TechnologyMngController extends AdminController
{
    #[Get('/technology', 'admin.technology')]
    public function index()
    {
        $technologies = TechnologyService::getAll();

        return inertia('Admin/Technology', [
            'meta' => [
                'title' => 'Tech stack',
            ],
            'technologyPage' => [
                'current' => null,
                'technologies' => array_map([$this, 'serializeTechnology'], $technologies),
            ],
        ]);
    }

    #[Get('/technology-edit/{id}', 'admin.technology.edit')]
    public function edit(int $id)
    {
        $technologies = TechnologyService::getAll();
        $technology = TechnologyService::findOrFail($id);

        return inertia('Admin/Technology', [
            'meta' => [
                'title' => 'Tech stack',
            ],
            'technologyPage' => [
                'current' => $this->serializeTechnology($technology),
                'technologies' => array_map([$this, 'serializeTechnology'], $technologies),
            ],
        ]);
    }

    #[Post('/technology', 'admin.technology.store')]
    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        if ($data instanceof Validator) {
            return response()->back()->withError($data->implodeError());
        }

        TechnologyService::create($data);

        return response()->back()->withSuccess('Tecnologia creata con successo.');
    }

    #[Patch('/technology-update/{id}', 'admin.technology.update')]
    public function update(Request $request, int $id)
    {
        $data = $this->validatePayload($request);

        if ($data instanceof Validator) {
            return response()->back()->withError($data->implodeError());
        }

        TechnologyService::update($id, $data);

        return response()->back()->withSuccess('Tecnologia aggiornata con successo.');
    }

    #[Delete('/technology-delete/{id}', 'admin.technology.delete')]
    public function destroy(int $id)
    {
        TechnologyService::delete($id);

        return response()->back()->withSuccess('Tecnologia eliminata con successo.');
    }

    /**
     * @return array{name: string}|Validator
     */
    private function validatePayload(Request $request): array|Validator
    {
        $icon = trim($request->string('icon'));

        $data = [
            'name' => trim($request->string('name')),
            'icon' => $icon !== '' ? $icon : null,
        ];

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
        ], [
            'name.required' => 'Il nome della tecnologia e obbligatorio.',
            'name.string' => 'Il nome della tecnologia deve essere una stringa.',
            'name.max' => 'Il nome della tecnologia non puo superare 100 caratteri.',
        ]);

        if ($validator->fails()) {
            return $validator;
        }

        return $data;
    }

    /**
     * @return array{
     *   id: int,
     *   icon: string|null,
     *   isActive: bool,
     *   name: string,
     *   sortOrder: int
     * }
     */
    private function serializeTechnology(object $technology): array
    {
        return [
            'id' => (int) ($technology->id ?? 0),
            'icon' => isset($technology->icon) && $technology->icon !== ''
                ? (string) $technology->icon
                : null,
            'isActive' => (bool) ($technology->is_active ?? false),
            'name' => (string) ($technology->name ?? ''),
            'sortOrder' => (int) ($technology->sort_order ?? 0),
        ];
    }
}
