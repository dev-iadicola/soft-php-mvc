<?php
namespace App\Controllers\Admin;


use App\Core\Controllers\AuthenticationController;
use App\Core\Http\Attributes\RouteAttr;
use App\Model\Skill;
use App\Core\Storage;
use App\Model\Article;
use App\Model\Profile;
use App\Core\Validator;
use App\Core\Http\Request;

class HomeManagerController extends AuthenticationController
{


    #[RouteAttr('/dashboard','get','admin.dashboard')]
    public function index()
    {
        // visualizza per la gestione della home
        $articles = Article::orderBy('created_at', 'DESC')->get();
        $skills = Skill::orderBy(' id', 'DESC')->get();
        $profiles = Profile::orderBy('id', 'DESC')->get();

        return view('admin.portfolio.home',  compact('articles','skills','profiles'));
    }


    public function store(Request $request)
    {
        $data = $request->all();
        $storage = new Storage();

        if ($data['img']['error'] === UPLOAD_ERR_NO_FILE) {
            unset($data['img']);
        } elseif ($data['img']['error'] !== UPLOAD_ERR_NO_FILE) {
            Validator::verifyImage($data['img']);
            $storage->disk('images')->put($data['img']);
           
            $data['img'] = $storage->getRelativePath();
        }

        Article::create($data);

        return response()->back()->withSuccess('Articolo Inserito con successo nella Home Page!');

        // inserisci un nuovo elemento
    }



    public function edit(Request $request, $id)
    {
       
        $article = Article::find($id);
        $articles = Article::orderBy('created_at', 'DESC')->get();
        $skills = Skill::orderBy('id', 'DESC')->get();
        $profiles = Profile::orderBy('id', 'DESC')->get();


        return view('admin.portfolio.home',  compact('articles', 'article', 'skills','profiles'));
    }
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $article = Article::find($id);
        if(isset($data['img'])){
            // Validazione Dati
        if ($data['img']['error'] === UPLOAD_ERR_NO_FILE) {
        
        }
        if ($data['img']['error'] !== UPLOAD_ERR_NO_FILE) {
            
            Validator::verifyImage($data['img']);
            $stg = new Storage();
            $stg->deleteIfFileExist($article->img);
            $stg->disk('images')->put($data['img']);
            $data['img'] = $stg->getRelativePath();
        } else {
            unset($data['img']);
        }
        }
        // Trova porgetto
        $project = Article::find($id);
        $project->update($data);

        // feedback server
        redirect()->back('Articolo Aggiornato con successo!');
    }
    #[RouteAttr('article-delete/{id}', 'DELETE', 'article.delete')]
    public function destroy(Request $reqq, $id)
    {
        // trova e azione
        $data =  $reqq->getPost();
    

        $project  = Article::find($id);


        $elem = $project->first();

        if(isset($elem->img)){
            $stg = new Storage();
            $stg->deleteIfFileExist($elem->img);
        }

        $project->delete();
        $this->redirectBack()->withSuccess('Articolo ELIMINATO');
    }
}
