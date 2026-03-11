<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\RouteAttr;
use App\Model\Certificate;
use App\Model\Curriculum;
use App\Model\Project;

class PortfolioController extends Controller {

    #[RouteAttr('portfolio')]
    public function index(): void {

        $projects = Project::query()->orderBy('id', 'DESC')->get();
        $certificati = Certificate::query()->orderBy('certified', 'DESC')->get();
        $curriculum = Curriculum::query()->orderBy('id','DESC')->first();
        view('portfolio', compact('projects','certificati', 'curriculum'));
      //  $this->render('portfolio',  compact('projects','certificati', 'curriculum'));
    }



}
