<?php
namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\RouteAttr;
use App\Model\Certificate;
use App\Model\Curriculum;
use App\Model\Project;

class PortfolioController extends Controller {

    #[RouteAttr('portfolio')]
    public function index() {
  
        $projects = Project::orderBy('id', 'DESC')->get();
        $certificati = Certificate::orderBy('certified', 'DESC')->get();
        $curriculum = Curriculum::orderBy(' id ','DESC')->first();
        $this->render('portfolio',  compact('projects','certificati', 'curriculum'));
    }



}