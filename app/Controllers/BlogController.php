<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Helpers\Seo;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Request;
use App\Support\Inertia\PublicPageSerializer;
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

        inertia('Public/Blog/Index', [
            'meta' => [
                'title' => $seo['title'],
            ],
            'page' => [
                'filters' => [
                    'search' => $search !== null ? (string) $search : '',
                    'tag' => $tag !== null ? (string) $tag : '',
                ],
                'pagination' => PublicPageSerializer::pagination($pagination),
                'tags' => array_map(
                    static fn(object $item): array => [
                        'id' => (int) ($item->id ?? 0),
                        'name' => (string) ($item->name ?? ''),
                        'slug' => (string) ($item->slug ?? ''),
                    ],
                    $tags
                ),
            ],
            'seo' => array_merge($seo, [
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'Blog',
                    'name' => $seo['title'],
                    'url' => $seo['url'],
                    'description' => $seo['description'],
                ],
            ]),
        ]);
    }

    #[Get('/blog/{slug}', 'blog.show')]
    public function show(string $slug): void
    {
        $slug = urldecode($slug);

        $article = is_numeric($slug)
            ? ArticleService::findOrFail((int) $slug)
            : ArticleService::findBySlug($slug);

        if ($article === null) {
            throw new \App\Core\Exception\NotFoundException("Article with slug {$slug} not found");
        }

        $related = array_filter(
            ArticleService::getActive(),
            static fn(object $item): bool => (int) ($item->id ?? 0) !== (int) ($article->id ?? 0)
        );
        $articlePayload = PublicPageSerializer::articleDetail($article);
        $articleUrl = Seo::baseUrl() . '/blog/' . rawurlencode((string) ($article->slug ?? $article->id ?? $slug));
        $articleDescription = $article->overview !== null
            ? trim(strip_tags(substr((string) $article->overview, 0, 160)))
            : ((string) ($article->subtitle ?? 'Dettaglio articolo del blog.'));
        $articleTags = array_map(
            static fn(array $tag): string => $tag['name'],
            $articlePayload['tags']
        );

        $seo = Seo::make([
            'title' => $article->title,
            'description' => $articleDescription,
            'image' => $article->img ?: null,
            'url' => $articleUrl,
        ]);

        inertia('Public/Blog/Show', [
            'meta' => [
                'title' => $seo['title'],
            ],
            'page' => [
                'article' => $articlePayload,
                'relatedArticles' => array_slice(
                    array_map([PublicPageSerializer::class, 'articleCard'], array_values($related)),
                    0,
                    6
                ),
            ],
            'seo' => array_merge($seo, [
                'type' => 'article',
                'published_time' => $articlePayload['createdAt'],
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => (string) ($article->title ?? ''),
                    'description' => $articleDescription,
                    'datePublished' => $articlePayload['createdAt'],
                    'image' => $article->img ?: $seo['image'],
                    'keywords' => array_values(array_filter($articleTags)),
                    'mainEntityOfPage' => $articleUrl,
                    'url' => $articleUrl,
                ],
            ]),
        ]);
    }
}
