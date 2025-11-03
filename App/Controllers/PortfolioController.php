<?php
namespace App\Controllers;
use App\Core\Controllers\BaseController;
use App\Core\Http\Attributes\RouteAttr;
use \App\Core\Mvc;
use App\Model\Certificate;
use App\Model\Curriculum;
use App\Model\Project;

class PortfolioController extends BaseController {

    public function __construct(public Mvc $mvc) {
        parent::__construct($mvc);
    }

    #[RouteAttr('portfolio')]
    public function index() {
  
        $projects = Project::orderBy('id', 'DESC')->get();
        $certificati = Certificate::orderBy('certified', 'DESC')->get();
        $curriculum = Curriculum::orderBy(' id ','DESC')->first();
        $this->render('portfolio',  compact('projects','certificati', 'curriculum'));
    }



}