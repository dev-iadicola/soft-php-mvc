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
        $seoDescription = $selectedTechnology !== null
            ? "Progetti di sviluppo web filtrati per {$selectedTechnology}."
            : 'Scopri i progetti di sviluppo web realizzati con PHP, Laravel, React e altre tecnologie.';
        $seo = Seo::make([
            'title' => 'Progetti',
            'description' => $seoDescription,
        ]);

        inertia('Public/Projects/Index', [
            'meta' => [
                'title' => $seo['title'],
            ],
            'page' => [
                'projects' => array_map([PublicPageSerializer::class, 'projectCard'], $projects),
                'selectedTechnology' => $selectedTechnology,
                'technologies' => array_map([PublicPageSerializer::class, 'technology'], $technologies),
            ],
            'seo' => array_merge($seo, [
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'CollectionPage',
                    'name' => $seo['title'],
                    'url' => $seo['url'],
                    'description' => $seo['description'],
                ],
            ]),
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
        $projectUrl = Seo::baseUrl() . '/progetti/' . rawurlencode((string) ($project->slug ?? $project->id ?? $slug));
        $projectDescription = $project->overview !== null
            ? trim(strip_tags(substr((string) $project->overview, 0, 160)))
            : 'Dettaglio progetto del portfolio.';
        $seo = Seo::make([
            'title' => $project->title,
            'description' => $projectDescription,
            'image' => $project->img ?: null,
            'url' => $projectUrl,
        ]);

        inertia('Public/Projects/Show', [
            'meta' => [
                'title' => $seo['title'],
            ],
            'page' => [
                'project' => PublicPageSerializer::projectDetail($project),
                'relatedProjects' => array_values(array_filter(
                    array_map([PublicPageSerializer::class, 'projectCard'], $projects),
                    static fn(array $item): bool => $item['id'] !== (int) ($project->id ?? 0)
                )),
            ],
            'seo' => array_merge($seo, [
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'CreativeWork',
                    'name' => (string) ($project->title ?? ''),
                    'url' => $projectUrl,
                    'description' => $projectDescription,
                    'image' => $project->img ?: $seo['image'],
                ],
            ]),
        ]);
    }
}
