<?php

declare(strict_types=1);

namespace App\Controllers\Admin;


use App\Core\Storage;
use App\Core\Http\Request;
use App\Core\Validation\Validator;
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
        $validator = $this->validateRequest($request);

        if ($validator->fails()) {
            return response()->back()->withError($validator->implodeError());
        }

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $storage->disk('images')->put($file);

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
        $validator = $this->validateRequest($request);

        if ($validator->fails()) {
            response()->back()->withError($validator->implodeError());
            return;
        }

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $stg = new Storage('images');
            $stg->deleteIfExist($article->img);
            $stg->disk('images')->put($file);
            $data['img'] = $stg->getRelativePath();
        } else {
            unset($data['img']);
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

    private function validateRequest(Request $request): Validator
    {
        $data = $request->all();
        $rules = [
            'title' => ['required', 'string'],
            'body' => ['nullable', 'string'],
            'img' => ['nullable'],
        ];

        if ($request->hasFile('img')) {
            $data['img'] = $request->file('img');
            $rules['img'] = ['image'];
        }

        return Validator::make($data, $rules, [
            'title.required' => 'Titolo richiesto.',
            'title.string' => 'Il titolo deve essere una stringa.',
            'body.string' => 'Il contenuto deve essere una stringa.',
            'img.image' => 'Immagine non valida.',
        ]);
    }
}
