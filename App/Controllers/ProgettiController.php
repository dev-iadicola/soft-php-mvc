<?php
namespace App\Controllers;
use \App\Core\Mvc;
use App\Model\Project;
use App\Model\Contatti;
use \App\Core\Component;

use \App\Core\Controller;
use App\Core\Controllers\BaseController;
use App\Core\Helpers\Log;
use App\Core\Http\Attributes\AttributeRoute;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;

class ProgettiController extends BaseController {

  
    #[RouteAttr('progetti')]
    public function index() {
     $projects = Project::findAll();
        
        $this->render(view: 'progetti',  variables: compact('projects' ));
    }

    #[RouteAttr('progetti/{id}')]
    public function show(Request $request, int $id){
         $project = Project::find($id);  // anziché usare findOrFail utilizzo l'eccezione all'interno della pagina pages.progetto 
         $projects = Project::findAll();
         view('progetto', compact('project', "projects"));
    }


}