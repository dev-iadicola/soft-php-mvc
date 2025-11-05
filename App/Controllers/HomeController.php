<?php
namespace App\Controllers;
use \App\Core\Mvc;
use App\Model\Skill;
use App\Model\Article;
use App\Model\Profile;
use App\Model\Project;
use App\Core\Controllers\BaseController;
use App\Model\Certificate;
use App\Core\Http\Attributes\RouteAttr;

class HomeController extends BaseController {


    public function __construct(public Mvc $mvc) {
        parent::__construct($mvc);
        
     
    }

    #[RouteAttr(path:'/')]
    public function index() {
        // recupero dati dal database
        $certificati = Certificate::orderBy('certified', 'DESC')->get();
        $projects = Project::orderBy(' id', 'DESC')->get();
        $articles = Article::orderBy('created_at', 'DESC')->get();
        $profiles = Profile::where('selected',TRUE)->orderBy('id', 'DESC')->get();
        $skills = Skill::orderBy('id', 'DESC')->get();


        view('home',compact('articles',
        'certificati','projects','profiles','skills') );
    }

    #[RouteAttr('/law')]
    public function cookie(){
        $this->render('cookie-law');
    }
    
    

}