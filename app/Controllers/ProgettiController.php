<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
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
        $projects = ProjectService::getAll(technology: $selectedTechnology);
        $technologies = TechnologyService::getAll();

        view('progetti', compact('projects', 'technologies', 'selectedTechnology'));
    }

    #[Get('progetti/{slug}')]
    public function show(string $slug): void
    {
        $slug = urldecode($slug);

        $project = is_numeric($slug)
            ? ProjectService::findOrFail((int) $slug)
            : ProjectService::findBySlug($slug);

        $projects = ProjectService::getAll();

        view('progetto', compact('project', 'projects'));
    }
}
