<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Facade\Storage;
use App\Core\Http\Attributes\Delete;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Patch;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Attributes\Post;
use App\Core\Http\Request;
use App\Core\Validation\Validator;
use App\Model\Project;
use App\Services\PartnerService;
use App\Services\ProjectService;
use App\Services\ProjectTechnologyService;
use App\Services\TechnologyService;

#[Prefix('/admin')]
#[Middleware('auth')]
class ProjectManagerController extends AdminController
{
    #[Get('project', 'admin.projects')]
    public function index()
    {
        $projects = ProjectService::getAll();

        return view('admin.portfolio.project', [
            'projects' => $projects,
            'project'  => null,
            'partners' => PartnerService::getAll(),
            'technologies' => TechnologyService::getAll(),
        ]);
    }

    #[Get('project-edit/{id}', 'admin.project.edit')]
    public function edit(int $id)
    {
        $project = ProjectService::findOrFail($id);
        $projects = ProjectService::getAll();

        return view('admin.portfolio.project', [
            'project' => $project,
            'projects' => $projects,
            'partners' => PartnerService::getAll(),
            'technologies' => TechnologyService::getAll(),
        ]);
    }

    #[Post('project-update/{id}', 'project.update')]
    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;
        $project = ProjectService::findOrFail($id);
        $validator = $this->validateRequest($request);

        if ($validator->fails()) {
            return redirect()->back()->withError($validator->implodeError());
        }

        $message = 'Progetto salvato con successo';

        // remove img from data if no new file uploaded, keep existing
        unset($data['img']);

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $filename = uniqid('project_') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            $path = 'images/' . $filename;
            $storage = Storage::make('public');

            // delete old image if present
            if (!empty($project->img)) {
                $oldPath = ltrim((string)$project->img, '/');
                if (str_starts_with($oldPath, 'storage/')) {
                    $oldPath = substr($oldPath, strlen('storage/'));
                }
                $storage->deleteIfExist($oldPath);
            }

            $storage->put(
                $path,
                file_get_contents($file['tmp_name']),
                ['visibility' => 'public']
            );

            $data['img'] = $storage->getPath($path);
        }

        ProjectService::update($id, $data);
        ProjectTechnologyService::syncForProject($id, $this->resolveTechnologyIds($request));

        return response()->back()->withSuccess($message);
    }

    #[Patch('project-upsert/{id}', 'admin.project.upset')]
    public function upset(Request $request, ?int $id = 0)
    {
        if ($id == 0) {
            return $this->store($request);
        }

        return $this->update($request, $id);

    }

    #[Post('project-store', 'admin.project.store')]
    public function store(Request $request)
    {
        // validate req
        $valid = $this->validateRequest($request);
        if ($valid->fails()) {
            return redirect()->back()->withError($valid->implodeError());
        }

        if (!$request->hasFile('img')) {
            return redirect()->back()->withError('Immagine obbligatoria per un nuovo progetto.');
        }

        // insert image to storage
        $file = $request->file('img');

        $filename = uniqid('project_') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $path = 'images/' . $filename;

        $storage = Storage::make('public');
        $storage->put(
            $path,
            file_get_contents($file['tmp_name']),
            ['visibility' => 'public']
        );


        $data = $valid->validated();
        $data['img'] = $storage->getPath($path);
        // Crea il progetto
        $project = ProjectService::create($data);
        ProjectTechnologyService::syncForProject((int) $project->getAttribute('id'), $this->resolveTechnologyIds($request));

        return redirect()->back()->withSuccess('Progetto salvato con Successo!');
    }

    #[Delete('project-delete/{id}', 'project.delete')]
    public function destroy(int $id)
    {
        $project = ProjectService::findOrFail($id);

        // delete img if exist
        $storage = Storage::make('public');
        $imgPath = ltrim((string)$project->img, '/');
        if (str_starts_with($imgPath, 'storage/app/public/')) {
            $imgPath = substr($imgPath, strlen('storage/app/public/'));
        }
        if ($storage->exists($imgPath)) {
            $storage->delete($imgPath);
        }

        ProjectService::delete($id);

        response()->back()->withSuccess('Progetto non eliminato correttamente.');
    }

    /**
     * Summary of validateRequest
     */
    private function validateRequest(Request $request): Validator
    {

        $data = $request->all();
        $rules = [
            'title'       => ['required', 'string'],
            'overview'    => ['required', 'string', 'max:499'],
            'description' => ['required', 'string'],
            'link'        => ['nullable'],
            'img'         => ['nullable'],
            'partner_id'  => ['nullable', 'integer'],
            'technology_id' => ['nullable', 'integer'],
            'website'     => ['nullable'],
        ];

        if ($request->hasFile('img')) {
            $data['img'] = $request->file('img');
            $rules['img'] = ['image'];
        }

        $validator = Validator::make($data, [
            ...$rules,
        ],
            [
                'title'       => 'Titolo Richiesto!',
                'overview'    => 'Sottititolo richieste!',
                'description' => 'Descrizione richiesta!',
                'img'         => 'Immagine non valida',
            ]
        );

        return $validator;
    }

    /**
     * @return array<int|string>
     */
    private function resolveTechnologyIds(Request $request): array
    {
        $technologyIds = $request->array('technology_ids');
        $primaryTechnologyId = $request->int('technology_id');

        if ($primaryTechnologyId > 0) {
            $technologyIds[] = $primaryTechnologyId;
        }

        return $technologyIds;
    }
}
