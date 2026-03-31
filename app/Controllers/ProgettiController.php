<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Helpers\Seo;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Request;
use App\Support\Inertia\PublicPageSerializer;
use App\Services\ProjectService;
use App\Services\TechnologyService;

class ProgettiController extends Controller
{
    #[Get('progetti')]
    public function index(Request $request): void
    {
        $selectedTechnology = trim((string) ($request->get('technology') ?? ''));
        $selectedTechnology = $selectedTechnology !== '' ? $selectedTechnology : null;
        $projects = ProjectService::getActive(technology: $selectedTechnology);
        $technologies = TechnologyService::getActive();
        $seo = Seo::make([
            'title' => 'Progetti',
            'description' => 'Scopri i progetti di sviluppo web realizzati con PHP, Laravel, React e altre tecnologie.',
        ]);

        inertia('Public/Projects/Index', [
            'meta' => [
                'title' => 'Progetti',
            ],
            'page' => [
                'projects' => array_map([PublicPageSerializer::class, 'projectCard'], $projects),
                'selectedTechnology' => $selectedTechnology,
                'technologies' => array_map([PublicPageSerializer::class, 'technology'], $technologies),
            ],
            'seo' => $seo,
        ]);
    }

    #[Get('progetti/{slug}')]
    public function show(string $slug): void
    {
        $slug = urldecode($slug);

        $project = is_numeric($slug)
            ? ProjectService::findOrFail((int) $slug)
            : ProjectService::findBySlug($slug);

        $projects = ProjectService::getActive();
        $seo = Seo::make([
            'title' => $project->title,
            'description' => $project->overview ? strip_tags(substr($project->overview, 0, 160)) : null,
            'image' => $project->img ?: null,
        ]);

        inertia('Public/Projects/Show', [
            'meta' => [
                'title' => (string) $project->title,
            ],
            'page' => [
                'project' => PublicPageSerializer::projectDetail($project),
                'relatedProjects' => array_values(array_filter(
                    array_map([PublicPageSerializer::class, 'projectCard'], $projects),
                    static fn(array $item): bool => $item['id'] !== (int) ($project->id ?? 0)
                )),
            ],
            'seo' => $seo,
        ]);
    }
}
