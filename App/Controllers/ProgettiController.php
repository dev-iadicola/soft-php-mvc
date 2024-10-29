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
        $projects = Project::where('deploy',1)->get();
        $gits = Project::where('deploy',0)->get();
        $this->render('progetti',[], compact('projects', 'gits'));
    }

    /**
     * Summary of getFormComponent
     * 
     * Inseriamo i componenti da inserire per sostotuire i placeholder
     */
    

   



}