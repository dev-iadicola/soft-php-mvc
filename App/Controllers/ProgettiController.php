<?php
namespace App\Controllers;
use \App\Core\Controller;
use \App\Core\Component;
use \App\Core\Mvc;

use App\Model\Contatti;
use App\Model\Project;

class ProgettiController extends Controller {

    public function __construct(public Mvc $mvc) {
        parent::__construct($mvc);
    }

    public function index() {
     $projects = Project::findAll();
        
        $this->render(view: 'progetti',  variables: compact('projects' ));
    }


}