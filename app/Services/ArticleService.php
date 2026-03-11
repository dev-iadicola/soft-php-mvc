<?php

declare(strict_types=1);

namespace App\Services;

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

    public static function create(array $data): Article
    {
        /** @var Article */
        return Article::query()->create($data);
    }

    public static function update(int $id, array $data): bool
    {
        return Article::query()->where('id', $id)->update($data);
    }

    public static function delete(int $id): void
    {
        Article::query()->where('id', $id)->delete();
    }
}
