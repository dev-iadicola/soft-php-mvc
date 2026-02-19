<?php
namespace App\Controllers;
use App\Model\Project;
use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;

class ProgettiController extends Controller {

  
    #[RouteAttr('progetti')]
    public function index() {
     $projects = Project::query()->all();

      view('progetti', compact('projects'));
    }

    #[RouteAttr('progetti/{slug}')]
    public function show(Request $request, string $slug): void{
         $project = is_numeric($slug)
             ? Project::query()->find((int) $slug)
             : Project::query()->where('title', $slug)->first();
         $projects = Project::query()->all();
         view('progetto', compact('project', "projects"));
    }


}