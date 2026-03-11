<?php

declare(strict_types=1);

namespace App\Controllers;

use \App\Core\Mvc;
use App\Model\Project;
use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\Get;
use App\Model\Certificate;
use App\Services\ArticleService;
use App\Services\SkillService;
use App\Services\ProfileService;

class HomeController extends Controller {




    #[Get('/')]
    public function index(): void {
        // recupero dati dal database
        $certificati = Certificate::query()->orderBy('certified', 'DESC')->get();
        $projects = Project::query()->orderBy(' id', 'DESC')->get();
        $articles = ArticleService::getAll();
        $profiles = ProfileService::getSelected();
        $skills = SkillService::getAll();


        view('home',compact('articles',
        'certificati','projects','profiles','skills') );
    }

    #[Get('/law')]
    public function cookie(): void {
        view('cookie-law');
    }



}
