<?php
namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Model\Skill;
use App\Model\Article;
use App\Model\Profile;

use App\Core\Controller;
use App\Core\Http\Request;

class HomeManagerController extends Controller
{

    public function __construct(public Mvc $mvc)
    {
        parent::__construct($mvc);

        $this->setLayout('admin');
    }

    public function index()
    {
        // visualizza per la gestione della home


        $articles = Article::orderBy('created_at DESC')->get();
        $skills = Skill::orderBy(' id DESC')->get();
        $profiles = Profile::orderBy('id DESC')->get();



        return $this->render('admin.portfolio.home', [], compact('articles','skills','profiles'));
    }


    public function store(Request $request)
    {
        $data = $request->getPost();



        if ($data['img']['error'] === UPLOAD_ERR_NO_FILE) {
            unset($data['img']);
        } elseif ($data['img']['error'] !== UPLOAD_ERR_NO_FILE) {
            $data['img'] = $this->checkImage($data);
        }

        Article::dirtySave($data);

        return $this->redirectBack()->withSuccess('Articolo Inserito con successo nella Home Page!');

        // inserisci un nuovo elemento
    }



    public function edit(Request $request, $id)
    {
        $article = Article::find($id);
        $articles = Article::orderBy('created_at DESC')->get();
        $skills = Skill::orderBy('id DESC')->get();
        $profiles = Profile::orderBy('id DESC')->get();


        return $this->render('admin.portfolio.home', [], compact('articles', 'article', 'skills','profiles'));
    }
    public function update(Request $request, $id)
    {
        $data = $request->getPost();
        $article = Article::find($id);
        if(isset($data['img'])){
            // Validazione Dati
        if ($data['img']['error'] === UPLOAD_ERR_NO_FILE) {
            
        }
        if ($data['img']['error'] !== UPLOAD_ERR_NO_FILE) {
            $this->deleteFile($article->img);

            $data['img'] = $this->checkImage($data);
        } else {
            unset($data['img']);
        }
        }
        

        // Trova porgetto
        $project = Article::find($id);
        $project->dirtyUpdate($data);


        // feedback server
        $this->withSuccess('Aggiornamento Eseguito');
        $this->redirectBack();
    }

    public function destroy(Request $reqq, $id)
    {
        // trova e azione
        $data =  $reqq->getPost();
        if (!isset($data['_method']) || !$data['_method'] === 'DELETE') {
            return $this->statusCode413();
        }

        $project  = Article::find($id);


        $elem = $project->first();

        if(isset($elem->img)){
            $this->deleteFile($elem->img);

        }


        $project->delete();
        $this->redirectBack()->withSuccess('Articolo ELIMINATO');
    }
}
