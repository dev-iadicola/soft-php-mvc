<?php

declare(strict_types=1);

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
        $articles = Article::query()->orderBy('created_at', 'DESC')->get();
        $skills = Skill::query()->orderBy(' id', 'DESC')->get();
        $profiles = Profile::query()->orderBy('id', 'DESC')->get();

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

        Article::query()->create($data);

        return response()->back()->withSuccess('Articolo Inserito con successo nella Home Page!');

        // inserisci un nuovo elemento
    }


    #[RouteAttr('article-edit/{id}', 'GET', 'article.edit')]
    public function edit(Request $request, string $id)
    {

        $article = Article::query()->find($id);
        $articles = Article::query()->orderBy('created_at', 'DESC')->get();
        $skills = Skill::query()->orderBy('id', 'DESC')->get();
        $profiles = Profile::query()->orderBy('id', 'DESC')->get();


        return view('admin.portfolio.home',  compact('articles', 'article', 'skills','profiles'));
    }

    #[RouteAttr('article-update','patch','article.update')]
    public function update(Request $request, string $id): void
    {
        $data = $request->all();
        $article = Article::query()->find($id);
        if(isset($data['img'])){
            // Validazione Dati
        if ($data['img']['error'] === UPLOAD_ERR_NO_FILE) {

        }
        if ($data['img']['error'] !== UPLOAD_ERR_NO_FILE) {

            Validator::verifyImage($data['img']);
            $stg = new Storage('images');
            $stg->deleteIfExist($article->img);
            $stg->disk('images')->put($data['img']);
            $data['img'] = $stg->getRelativePath();
        } else {
            unset($data['img']);
        }
        }
        // Trova porgetto
        Article::query()->where('id', $id)->update($data);

        // feedback server
        redirect()->back('Articolo Aggiornato con successo!');
    }

    #[RouteAttr('article-delete/{id}', 'DELETE', 'article.delete')]
        public function destroy(Request $reqq, string $id)
    {
        // TODO: make validator 
    
        $article  = Article::query()->find($id);
        $name = $article->title;

        if(isset($article->img)){
            $stg = new Storage('images');
            $stg->deleteIfExist($article->img);
        }
        Article::query()->where('id', $id)->delete();
        return response()->back()->withSuccess('Articolo $name eliminato.');
    }
}
