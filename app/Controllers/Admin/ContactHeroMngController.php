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
use App\Services\ContactHeroService;

#[Prefix('/admin')]
#[Middleware('auth')]
class ContactHeroMngController extends AdminController
{
    #[Get('/contact-hero', 'admin.contact-hero')]
    public function index()
    {
        return view('admin.portfolio.contact-hero', [
            'heroes' => ContactHeroService::getAll(),
            'item' => null,
        ]);
    }

    #[Get('/contact-hero-edit/{id}', 'admin.contact-hero.edit')]
    public function edit(int $id)
    {
        return view('admin.portfolio.contact-hero', [
            'heroes' => ContactHeroService::getAll(),
            'item' => ContactHeroService::findOrFail($id),
        ]);
    }

    #[Post('/contact-hero', 'admin.contact-hero.store')]
    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        if ($data instanceof Validator) {
            return response()->back()->withError($data->implodeError());
        }

        ContactHeroService::create($data);

        return response()->back()->withSuccess('Hero contatti creato con successo.');
    }

    #[Patch('/contact-hero-update/{id}', 'admin.contact-hero.update')]
    public function update(Request $request, int $id)
    {
        $data = $this->validatePayload($request);

        if ($data instanceof Validator) {
            return response()->back()->withError($data->implodeError());
        }

        ContactHeroService::update($id, $data);

        return response()->back()->withSuccess('Hero contatti aggiornato con successo.');
    }

    #[Delete('/contact-hero-delete/{id}', 'admin.contact-hero.delete')]
    public function destroy(int $id)
    {
        ContactHeroService::delete($id);

        return response()->back()->withSuccess('Hero contatti eliminato con successo.');
    }

    /**
     * @return array<string, string>|Validator
     */
    private function validatePayload(Request $request): array|Validator
    {
        $data = [
            'badge' => trim($request->string('badge')),
            'title_html' => trim($request->string('title_html')),
            'description_html' => trim($request->string('description_html')),
            'primary_stat_value' => trim($request->string('primary_stat_value')),
            'primary_stat_label' => trim($request->string('primary_stat_label')),
            'secondary_stat_value' => trim($request->string('secondary_stat_value')),
            'secondary_stat_label' => trim($request->string('secondary_stat_label')),
            'technology_stat_label' => trim($request->string('technology_stat_label')),
        ];

        $validator = Validator::make($data, [
            'badge' => ['required', 'string', 'max:100'],
            'title_html' => ['required', 'string'],
            'description_html' => ['required', 'string'],
            'primary_stat_value' => ['required', 'string', 'max:50'],
            'primary_stat_label' => ['required', 'string', 'max:100'],
            'secondary_stat_value' => ['required', 'string', 'max:50'],
            'secondary_stat_label' => ['required', 'string', 'max:100'],
            'technology_stat_label' => ['required', 'string', 'max:100'],
        ], [
            'badge.required' => 'Il badge e obbligatorio.',
            'title_html.required' => 'Il titolo hero e obbligatorio.',
            'description_html.required' => 'La descrizione hero e obbligatoria.',
            'primary_stat_value.required' => 'Il valore della prima statistica e obbligatorio.',
            'primary_stat_label.required' => 'L etichetta della prima statistica e obbligatoria.',
            'secondary_stat_value.required' => 'Il valore della seconda statistica e obbligatorio.',
            'secondary_stat_label.required' => 'L etichetta della seconda statistica e obbligatoria.',
            'technology_stat_label.required' => 'L etichetta della statistica tecnologie e obbligatoria.',
        ]);

        if ($validator->fails()) {
            return $validator;
        }

        return $data;
    }
}
