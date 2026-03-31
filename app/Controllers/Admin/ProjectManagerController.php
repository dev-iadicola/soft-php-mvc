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
use App\Services\MediaService;
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
            'gallery' => [],
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
            'gallery' => MediaService::getFor('project', $id),
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

        // Remove img from data if no new file uploaded, keep existing
        unset($data['img']);

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $mainMedia = MediaService::attach('project_img', $id, $file);

            // Delete old image if present
            if (!empty($project->img)) {
                $oldPath = ltrim((string) $project->img, '/');
                if (str_starts_with($oldPath, 'storage/')) {
                    $oldPath = substr($oldPath, strlen('storage/'));
                }
                Storage::make('public')->deleteIfExist($oldPath);
            }

            $data['img'] = $mainMedia->path;
        }

        // Gallery images
        $this->attachGalleryFiles($request, $id);

        ProjectService::update($id, $data);
        ProjectTechnologyService::syncForProject($id, $this->resolveTechnologyIds($request));

        return response()->back()->withSuccess('Progetto salvato con successo');
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
        $valid = $this->validateRequest($request);
        if ($valid->fails()) {
            return redirect()->back()->withError($valid->implodeError());
        }

        if (!$request->hasFile('img')) {
            return redirect()->back()->withError('Immagine obbligatoria per un nuovo progetto.');
        }

        // Main image via MediaService
        $file = $request->file('img');
        $mainMedia = MediaService::attach('project_img', 0, $file);

        $data = $valid->validated();
        $data['img'] = $mainMedia->path;

        $project = ProjectService::create($data);
        $projectId = (int) $project->getAttribute('id');

        // Update main media entity_id now that we have the project ID
        MediaService::reorder($mainMedia->id, 0);

        // Gallery images
        $this->attachGalleryFiles($request, $projectId);

        ProjectTechnologyService::syncForProject($projectId, $this->resolveTechnologyIds($request));

        return redirect()->back()->withSuccess('Progetto salvato con Successo!');
    }

    #[Delete('project-delete/{id}', 'project.delete')]
    public function destroy(int $id)
    {
        $project = ProjectService::findOrFail($id);

        // Delete main image
        if (!empty($project->img)) {
            $imgPath = ltrim((string) $project->img, '/');
            if (str_starts_with($imgPath, 'storage/')) {
                $imgPath = substr($imgPath, strlen('storage/'));
            }
            Storage::make('public')->deleteIfExist($imgPath);
        }

        // Delete gallery
        MediaService::deleteAllFor('project', $id);

        ProjectService::delete($id);

        return response()->back()->withSuccess('Progetto eliminato correttamente.');
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

    #[Delete('project-media/{mediaId}', 'project.media.delete')]
    public function deleteMedia(int $mediaId)
    {
        MediaService::delete($mediaId);
        return response()->back()->withSuccess('Immagine eliminata.');
    }

    /**
     * Attach gallery files from a multi-file input (name="gallery[]").
     */
    private function attachGalleryFiles(Request $request, int $projectId): void
    {
        $galleryFiles = $_FILES['gallery'] ?? null;

        if ($galleryFiles === null || !is_array($galleryFiles['name'] ?? null)) {
            return;
        }

        $count = count($galleryFiles['name']);

        for ($i = 0; $i < $count; $i++) {
            if (($galleryFiles['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                continue;
            }

            $file = [
                'name' => $galleryFiles['name'][$i],
                'tmp_name' => $galleryFiles['tmp_name'][$i],
                'type' => $galleryFiles['type'][$i] ?? '',
                'size' => $galleryFiles['size'][$i] ?? 0,
                'error' => $galleryFiles['error'][$i],
            ];

            MediaService::attach('project', $projectId, $file);
        }
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
