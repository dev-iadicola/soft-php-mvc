<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Helpers\Seo;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Request;
use App\Services\ProjectService;
use App\Services\TechnologyService;

class ProgettiController extends Controller
{
    #[Get('progetti')]
    public function index(Request $request): void
    {
        $selectedTechnology = isset($_GET['technology']) ? trim((string) $_GET['technology']) : null;
        $projects = ProjectService::getActive(technology: $selectedTechnology);
        $technologies = TechnologyService::getActive();
        $seo = Seo::make([
            'title' => 'Progetti',
            'description' => 'Scopri i progetti di sviluppo web realizzati con PHP, Laravel, React e altre tecnologie.',
        ]);

        view('progetti', compact('projects', 'technologies', 'selectedTechnology', 'seo'));
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

        view('progetto', compact('project', 'projects', 'seo'));
    }
}
