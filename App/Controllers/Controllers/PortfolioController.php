<?php
namespace App\Controllers;
use \App\Core\Controller;
use \App\Core\Component;
use \App\Core\Mvc;
use App\Model\Certificate;
use App\Model\Curriculum;
use App\Model\Portfolio;
use App\Model\Project;

class PortfolioController extends Controller {

    public function __construct(public Mvc $mvc) {
        parent::__construct($mvc);
    }

    public function index() {
        $portfolioModel = Portfolio::findAll();
        $items = $portfolioModel;
        $portfolio = Portfolio::orderBy('id DESC')->get();
        $projects = Project::orderBy('id DESC')->get();
        $certificati = Certificate::orderBy('certified DESC')->get();
        $curriculum = Curriculum::orderBy(' id DESC')->first();
        $this->render('portfolio', [], compact('portfolio','projects','certificati', 'curriculum'));
    }



}