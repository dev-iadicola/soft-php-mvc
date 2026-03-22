<?php

declare(strict_types=1);

namespace App\Controllers\Admin;


use App\Core\Storage;
use App\Core\Http\Request;
use App\Core\Http\Attributes\Delete;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Patch;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Attributes\Post;
use App\Core\Validation\Validator;
use App\Services\ArticleService;
use App\Services\SkillService;
use App\Services\ProfileService;
use App\Core\Controllers\AdminController;

#[Prefix('/admin')]
#[Middleware('auth')]
class HomeManagerController extends AdminController
{


    #[Get('/home', 'admin.home')]
    public function index()
    {
        // visualizza per la gestione della home
        $articles = ArticleService::getAll();
        $skills = SkillService::getAll();
        $profiles = ProfileService::getAll();

        return view('admin.portfolio.home',  compact('articles','skills','profiles'));
    }

    #[Post('/article-store', 'article.store')]
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


    #[Get('article-edit/{id}', 'article.edit')]
    public function edit(int $id)
    {

        $article = ArticleService::findOrFail($id);
        $articles = ArticleService::getAll();
        $skills = SkillService::getAll();
        $profiles = ProfileService::getAll();


        return view('admin.portfolio.home',  compact('articles', 'article', 'skills','profiles'));
    }

    #[Patch('article-update', 'article.update')]
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

    #[Delete('article-delete/{id}', 'article.delete')]
        public function destroy(int $id)
    {
        $validator = Validator::make(
            ['id' => $id],
            ['id' => ['required', 'integer', 'min:1']],
            [
                'id.required' => 'ID articolo mancante.',
                'id.integer' => 'ID articolo non valido.',
                'id.min' => 'ID articolo non valido.',
            ]
        );

        if ($validator->fails()) {
            return response()->back()->withError($validator->implodeError());
        }

        $article = ArticleService::findOrFail($id);
        $name = $article->title;

        if(isset($article->img)){
            $stg = new Storage('images');
            $stg->deleteIfExist($article->img);
        }
        ArticleService::delete($id);
        return response()->back()->withSuccess("Articolo {$name} eliminato.");
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
