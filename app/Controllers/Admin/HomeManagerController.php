<?php

declare(strict_types=1);

namespace App\Controllers\Admin;


use App\Core\Storage;
use App\Core\Validator;
use App\Core\Http\Request;
use App\Services\ArticleService;
use App\Services\SkillService;
use App\Services\ProfileService;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Controllers\AdminController;

class HomeManagerController extends AdminController
{


    #[RouteAttr('/home','get','admin.home')]
    public function index()
    {
        // visualizza per la gestione della home
        $articles = ArticleService::getAll();
        $skills = SkillService::getAll();
        $profiles = ProfileService::getAll();

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

        ArticleService::create($data);

        return response()->back()->withSuccess('Articolo Inserito con successo nella Home Page!');
    }


    #[RouteAttr('article-edit/{id}', 'GET', 'article.edit')]
    public function edit(Request $request, string $id)
    {

        $article = ArticleService::findOrFail((int) $id);
        $articles = ArticleService::getAll();
        $skills = SkillService::getAll();
        $profiles = ProfileService::getAll();


        return view('admin.portfolio.home',  compact('articles', 'article', 'skills','profiles'));
    }

    #[RouteAttr('article-update','patch','article.update')]
    public function update(Request $request, string $id): void
    {
        $data = $request->all();
        $article = ArticleService::findOrFail((int) $id);
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
        ArticleService::update((int) $id, $data);

        // feedback server
        redirect()->back('Articolo Aggiornato con successo!');
    }

    #[RouteAttr('article-delete/{id}', 'DELETE', 'article.delete')]
        public function destroy(Request $reqq, string $id)
    {
        // TODO: make validator

        $article = ArticleService::findOrFail((int) $id);
        $name = $article->title;

        if(isset($article->img)){
            $stg = new Storage('images');
            $stg->deleteIfExist($article->img);
        }
        ArticleService::delete((int) $id);
        return response()->back()->withSuccess('Articolo $name eliminato.');
    }
}
