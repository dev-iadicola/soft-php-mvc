<?php
namespace App\Controllers;
use App\Core\Eloquent\Model;
use \App\Core\Mvc;
use App\Model\Skill;
use App\Model\Article;
use App\Model\Profile;
use App\Model\Project;
use \App\Core\Controller;
use App\Model\Curriculum;
use App\Model\Certificate;

class HomeController extends Controller {

    public function __construct(public Mvc $mvc) {
        parent::__construct($mvc);
        
     
    }

    public function index() {
        // recupero dati dal database

        $certificati = Certificate::orderBy('certified DESC')->get();
        $projects = Project::orderBy(' id DESC')->get();
        $articles = Article::orderBy('created_at DESC')->get();
        $curriculum = Curriculum::orderBy(' id DESC')->first();
        $profiles = (new Model($this->mvc->pdo))->query('select * FROM profile where selected is true ORDER BY id desc');
       // $profiles = Profile::orderBy('id desc')->where('selected', true)->get();
        $skills = Skill::orderBy('id desc')->get();


        $this->render('home',[] ,compact('articles','certificati','projects','curriculum','profiles','skills') );
    }

    public function cookie(){
        $this->render('cookie-law');
    }
    
    

}