<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;
use App\Services\ProjectService;

class ProgettiController extends Controller
{
    #[RouteAttr('progetti')]
    public function index(): void
    {
        $projects = ProjectService::getAll();

        view('progetti', compact('projects'));
    }

    #[RouteAttr('progetti/{slug}')]
    public function show(Request $request, string $slug): void
    {
        $slug = urldecode($slug);

        $project = is_numeric($slug)
            ? ProjectService::findOrFail((int) $slug)
            : ProjectService::findBySlug($slug);

        $projects = ProjectService::getAll();

        view('progetto', compact('project', 'projects'));
    }
}
