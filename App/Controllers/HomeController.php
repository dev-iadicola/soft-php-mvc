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
// TODO: PROVALO PER TESTARE IL LOGGER 'app.log' file_get_contents(filename: 'file_che_non_esiste.txt');

        $certificati = Certificate::orderBy('certified', 'DESC')->get();
        $projects = Project::orderBy(' id', 'DESC')->get();
        $articles = Article::orderBy('created_at', 'DESC')->get();
        $profiles = Profile::where('selected',TRUE)->orderBy('id', 'DESC')->get();
        $skills = Skill::orderBy('id', 'DESC')->get();


        view('home',compact('articles',
        'certificati','projects','profiles','skills') );
    }

    public function cookie(){
        $this->render('cookie-law');
    }
    
    

}