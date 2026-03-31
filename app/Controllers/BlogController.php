<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Helpers\Seo;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Request;
use App\Services\ArticleService;
use App\Services\TagService;

class BlogController extends Controller
{
    #[Get('/blog', 'blog')]
    public function index(Request $request): void
    {
        $page = max(1, (int) ($request->get('page') ?? 1));
        $search = $request->get('search');
        $tag = $request->get('tag');

        $pagination = ArticleService::paginateActive(6, $page, $search, $tag);
        $tags = TagService::getAll();

        $seo = Seo::make([
            'title' => 'Blog',
            'description' => 'Articoli, guide e riflessioni sullo sviluppo software.',
        ]);

        view('blog', compact('pagination', 'tags', 'search', 'tag', 'seo'));
    }
}
