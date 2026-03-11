<?php

declare(strict_types=1);

namespace App\Controllers;
use \App\Core\Mvc;
use App\Model\Skill;
use App\Model\Article;
use App\Model\Profile;
use App\Model\Project;
use App\Core\Controllers\Controller;
use App\Model\Certificate;
use App\Core\Http\Attributes\RouteAttr;

class HomeController extends Controller {


    

    #[RouteAttr(path:'/')]
    public function index(): void {
        // recupero dati dal database
        $certificati = Certificate::query()->orderBy('certified', 'DESC')->get();
        $projects = Project::query()->orderBy(' id', 'DESC')->get();
        $articles = Article::query()->orderBy('created_at', 'DESC')->get();
        $profiles = Profile::query()->where('selected',TRUE)->orderBy('id', 'DESC')->get();
        $skills = Skill::query()->orderBy('id', 'DESC')->get();


        view('home',compact('articles',
        'certificati','projects','profiles','skills') );
    }

    #[RouteAttr('/law')]
    public function cookie(): void {
        $this->render('cookie-law');
    }
    
    

}
