<?php

declare(strict_types=1);

namespace App\Services;

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
