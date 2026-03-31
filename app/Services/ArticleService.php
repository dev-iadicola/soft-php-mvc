<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\DataLayer\PaginationResult;
use App\Core\Helpers\Str;
use App\Model\Article;
use App\Core\Exception\NotFoundException;

class ArticleService
{
    /**
     * @return array<int, Article>
     */
    public static function getAll(string $orderBy = 'created_at', string $order = 'DESC'): array
    {
        return Article::query()->orderBy($orderBy, $order)->get();
    }

    /**
     * @return array<int, Article>
     */
    public static function getActive(string $orderBy = 'created_at', string $order = 'DESC'): array
    {
        return Article::query()->where('is_active', true)->orderBy($orderBy, $order)->get();
    }

    public static function paginateActive(int $perPage = 6, int $page = 1, ?string $search = null, ?string $tag = null): PaginationResult
    {
        $query = Article::query()->where('is_active', true);

        if ($search !== null && $search !== '') {
            $query->where('title', 'LIKE', "%{$search}%");
        }

        $totalItems = $query->count();

        // Rebuild query (count resets builder)
        $query = Article::query()->where('is_active', true);

        if ($search !== null && $search !== '') {
            $query->where('title', 'LIKE', "%{$search}%");
        }

        if ($tag !== null && $tag !== '') {
            $query->select('articles.*')
                ->from('articles')
                ->join('article_tag', 'article_tag.article_id', '=', 'articles.id')
                ->join('tags', 'tags.id', '=', 'article_tag.tag_id')
                ->where('tags.slug', $tag);
        }

        $items = $query
            ->orderBy('created_at', 'DESC')
            ->limit($perPage)
            ->offset(($page - 1) * $perPage)
            ->get();

        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        return new PaginationResult($items, $page, $totalPages, $totalItems, $perPage);
    }

    /**
     * @return array<int, Article>
     */
    public static function search(string $term): array
    {
        return Article::query()
            ->where('is_active', true)
            ->where('title', 'LIKE', "%{$term}%")
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * @throws NotFoundException
     */
    public static function findOrFail(int $id): Article
    {
        $article = Article::query()->find($id);

        if ($article === null) {
            throw new NotFoundException("Article with id {$id} not found");
        }

        /** @var Article $article */
        return $article;
    }

    public static function findBySlug(string $slug): ?Article
    {
        /** @var Article|null */
        return Article::query()->where('slug', $slug)->first();
    }

    public static function create(array $data): Article
    {
        if (!isset($data['slug']) && isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        /** @var Article */
        return Article::query()->create($data);
    }

    public static function update(int $id, array $data): bool
    {
        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return Article::query()->where('id', $id)->update($data);
    }

    public static function delete(int $id): void
    {
        Article::query()->where('id', $id)->delete();
    }
}
