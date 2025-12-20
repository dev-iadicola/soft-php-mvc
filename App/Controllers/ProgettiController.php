<?php
namespace App\Controllers;
use \App\Core\Mvc;
use App\Model\Project;
use App\Model\Contatti;
use \App\Core\Component;
use App\Core\Controllers\Controller;
use App\Core\Helpers\Log;
use App\Core\Http\Attributes\AttributeRoute;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;

class ProgettiController extends Controller {

  
    #[RouteAttr('progetti')]
    public function index() {
     $projects = Project::all();
        
      view('progetti', compact('projects'));
    }

    #[RouteAttr('progetti/{title}')]
    public function show(Request $request, int $id): void{
         $project = Project::find($id);  
         $projects = Project::all();
         view('progetto', compact('project', "projects"));
    }


}