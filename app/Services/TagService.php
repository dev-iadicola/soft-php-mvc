<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Exception\NotFoundException;
use App\Core\Helpers\Str;
use App\Model\ArticleTag;
use App\Model\Tag;

class TagService
{
    /**
     * @return array<int, Tag>
     */
    public static function getAll(): array
    {
        return Tag::query()->orderBy('name')->get();
    }

    public static function findOrFail(int $id): Tag
    {
        /** @var Tag|null $tag */
        $tag = Tag::query()->find($id);

        if ($tag === null) {
            throw new NotFoundException("Tag with id {$id} not found");
        }

        return $tag;
    }

    public static function findBySlug(string $slug): ?Tag
    {
        /** @var Tag|null */
        return Tag::query()->where('slug', $slug)->first();
    }

    public static function create(array $data): Tag
    {
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        Tag::query()->create($data);

        /** @var Tag */
        return Tag::query()->orderBy('id', 'DESC')->first();
    }

    public static function update(int $id, array $data): bool
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return Tag::query()->where('id', $id)->update($data);
    }

    public static function delete(int $id): void
    {
        // Remove pivot entries first
        ArticleTag::query()->where('tag_id', $id)->delete();
        Tag::query()->where('id', $id)->delete();
    }

    /**
     * Get all tags for an article.
     *
     * @return array<int, Tag>
     */
    public static function getForArticle(int $articleId): array
    {
        /** @var array<int, Tag> */
        return Tag::query()
            ->select('tags.*')
            ->from('tags')
            ->join('article_tag', 'article_tag.tag_id', '=', 'tags.id')
            ->where('article_tag.article_id', $articleId)
            ->orderBy('tags.name')
            ->get();
    }

    /**
     * Sync tags for an article (replace all).
     *
     * @param array<int> $tagIds
     */
    public static function syncForArticle(int $articleId, array $tagIds): void
    {
        ArticleTag::query()->where('article_id', $articleId)->delete();

        foreach ($tagIds as $tagId) {
            ArticleTag::query()->create([
                'article_id' => $articleId,
                'tag_id' => (int) $tagId,
            ]);
        }
    }
}
