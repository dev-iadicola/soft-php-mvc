<?php
namespace App\Controllers\Admin;


use App\Model\Skill;
use App\Core\Storage;
use App\Model\Article;
use App\Model\Profile;
use App\Core\Validator;
use App\Core\Http\Request;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Controllers\AdminController;
use App\Core\Controllers\AuthenticationController;

class HomeManagerController extends AdminController
{


    #[RouteAttr('/home','get','admin.home')]
    public function index()
    {
        // visualizza per la gestione della home
        $articles = Article::orderBy('created_at', 'DESC')->get();
        $skills = Skill::orderBy(' id', 'DESC')->get();
        $profiles = Profile::orderBy('id', 'DESC')->get();

        return view('admin.portfolio.home',  compact('articles','skills','profiles'));
    }

    #[RouteAttr('/article-store','POST','article.store')]
    public function store(Request $request)
    {
        $data = $request->all();
        $storage = new Storage('images');

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


    #[RouteAttr('article-edit/{id}', 'GET', 'article.edit')]
    public function edit(Request $request, $id)
    {
       
        $article = Article::find($id);
        $articles = Article::orderBy('created_at', 'DESC')->get();
        $skills = Skill::orderBy('id', 'DESC')->get();
        $profiles = Profile::orderBy('id', 'DESC')->get();


        return view('admin.portfolio.home',  compact('articles', 'article', 'skills','profiles'));
    }

    #[RouteAttr('article-update','patch','article.update')]
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
            $stg = new Storage('images');
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
        // TODO: make validator 
    
        $article  = Article::find($id);
        $name = $article->title;

        $elem = $article->first();

        if(isset($elem->img)){
            $stg = new Storage('images');
            $stg->deleteIfFileExist($elem->img);
        }
        $article->delete();
        return response()->back()->withSuccess('Articolo $name eliminato.');
    }
}
