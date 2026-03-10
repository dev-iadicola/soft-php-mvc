<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Facade\Storage;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;
use App\Core\Validation\Validator;
use App\Model\Project;

class ProjectManagerController extends AdminController
{
    #[RouteAttr(path: 'project', method: 'get', name: 'admin.projects')]
    public function index()
    {
        $projects = Project::query()->orderBy('id', 'DESC')->get();


        return view('admin.portfolio.project', [
            'projects' => $projects,
            'project'  => null,
        ]);
    }

    #[RouteAttr(path: 'project-edit/{id}', method: 'get', name: 'admin.project.edit')]
    public function edit(Request $request, int $id)
    {

        $project = Project::query()->find($id);
        $projects = Project::query()->orderBy('id', 'DESC')->get();

        return view('admin.portfolio.project', compact('project', 'projects'));
    }

    #[RouteAttr(path: 'project-update/{id}', method: 'POST', name: 'project.update')]
    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $project = Project::query()->find($id);
        $validator = $this->validateRequest($request);

        if ($validator->fails()) {
            return redirect()->back()->withError($validator->implodeError());
        }

        $message = 'Progetto salvato con successo';

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $filename = uniqid('project_') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            $path = 'images/' . $filename;
            $storage = Storage::make('public');

            // delete old image if present
            if (!empty($project->img)) {
                $oldPath = ltrim((string)$project->img, '/');
                if (str_starts_with($oldPath, 'storage/app/public/')) {
                    $oldPath = substr($oldPath, strlen('storage/app/public/'));
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

        Project::query()->where('id', $id)->update($data);

        return response()->back()->withSuccess($message);
    }

    #[RouteAttr('project-upsert/{id}', 'PATCH', 'admin.project.upset')]
    public function upset(Request $request, ?int $id = 0)
    {
        if ($id == 0) {
            return $this->store($request);
        }

        return $this->update($request, $id);

    }

    #[RouteAttr(path: 'project-store', method: 'POST', name: 'admin.project.store')]
    public function store(Request $request)
    {
        // validate req
        $valid = $this->validateRequest($request);
        if ($valid->fails()) {
            return redirect()->back()->withError($valid->implodeError());
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
        Project::query()->create($data);

        return redirect()->back()->withSuccess('Progetto salvato con Successo!');
    }

    #[RouteAttr(path: 'project-delete/{id}', method: 'DELETE', name: 'project.delete')]
    public function destroy(Request $reqq, int $id)
    {
        // trova e azione
        $data = $reqq->all();

        $projectQ = Project::query()->where('id', $id);
        if ( ! $projectQ->exists()) {
            return response()->back()->withError('Progetto non trovato');
        }
        // delete img if exist
        $project = Project::query()->find($id);
        $storage = Storage::make('public');
        $imgPath = ltrim((string)$project->img, '/');
        if (str_starts_with($imgPath, 'storage/app/public/')) {
            $imgPath = substr($imgPath, strlen('storage/app/public/'));
        }
        if ($storage->exists($imgPath)) {
            $storage->delete($imgPath);
        }

        Project::query()->where('id', $id)->delete();

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
}
