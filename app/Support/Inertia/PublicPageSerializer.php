<?php

declare(strict_types=1);

namespace App\Support\Inertia;

use App\Core\DataLayer\PaginationResult;
use App\Services\MediaService;
use App\Services\TagService;

class PublicPageSerializer
{
    /**
     * @return array{
     *   id: int,
     *   avatar: string|null,
     *   bio: string|null,
     *   githubUrl: string|null,
     *   linkedinUrl: string|null,
     *   name: string,
     *   tagline: string|null,
     *   twitterUrl: string|null,
     *   welcomeMessage: string|null
     * }
     */
    public static function profile(object $profile): array
    {
        return [
            'id' => (int) ($profile->id ?? 0),
            'avatar' => self::nullableString($profile->avatar ?? null),
            'bio' => self::nullableString($profile->bio ?? null),
            'githubUrl' => self::nullableString($profile->github_url ?? null),
            'linkedinUrl' => self::nullableString($profile->linkedin_url ?? null),
            'name' => (string) ($profile->name ?? ''),
            'tagline' => self::nullableString($profile->tagline ?? null),
            'twitterUrl' => self::nullableString($profile->twitter_url ?? null),
            'welcomeMessage' => self::nullableString($profile->welcome_message ?? null),
        ];
    }

    /**
     * @return array{
     *   id: int,
     *   description: string|null,
     *   title: string
     * }
     */
    public static function skill(object $skill): array
    {
        return [
            'id' => (int) ($skill->id ?? 0),
            'description' => self::nullableString($skill->description ?? null),
            'title' => (string) ($skill->title ?? ''),
        ];
    }

    /**
     * @return array{
     *   id: int,
     *   certifiedAt: string|null,
     *   company: string|null,
     *   img: string|null,
     *   title: string
     * }
     */
    public static function certificate(object $certificate): array
    {
        return [
            'id' => (int) ($certificate->id ?? 0),
            'certifiedAt' => self::nullableString($certificate->certified ?? null),
            'company' => self::nullableString($certificate->company ?? null),
            'img' => self::nullableString($certificate->img ?? null),
            'title' => (string) ($certificate->title ?? ''),
        ];
    }

    /**
     * @return array{
     *   id: int,
     *   icon: string|null,
     *   name: string
     * }
     */
    public static function technology(object $technology): array
    {
        return [
            'id' => (int) ($technology->id ?? 0),
            'icon' => self::nullableString($technology->icon ?? null),
            'name' => (string) ($technology->name ?? ''),
        ];
    }

    /**
     * @return array{
     *   id: int,
     *   img: string|null,
     *   link: string|null,
     *   overview: string|null,
     *   partnerName: string|null,
     *   slug: string,
     *   startedAt: string|null,
     *   endedAt: string|null,
     *   technologies: array<int, array{id: int, icon: string|null, name: string}>,
     *   title: string,
     *   website: string|null
     * }
     */
    public static function projectCard(object $project): array
    {
        $slug = self::projectSlug($project);
        $technologies = method_exists($project, 'technologies') ? $project->technologies() : [];

        return [
            'id' => (int) ($project->id ?? 0),
            'img' => self::nullableString($project->img ?? null),
            'link' => self::nullableString($project->link ?? null),
            'overview' => self::nullableString($project->overview ?? null),
            'partnerName' => isset($project->partner) && is_object($project->partner)
                ? self::nullableString($project->partner->name ?? null)
                : null,
            'slug' => $slug,
            'startedAt' => self::nullableString($project->started_at ?? null),
            'endedAt' => self::nullableString($project->ended_at ?? null),
            'technologies' => array_map([self::class, 'technology'], $technologies),
            'title' => (string) ($project->title ?? ''),
            'website' => self::nullableString($project->website ?? null),
        ];
    }

    /**
     * @return array{
     *   createdAt: string|null,
     *   description: string|null,
     *   endedAt: string|null,
     *   gallery: array<int, array{path: string}>,
     *   id: int,
     *   img: string|null,
     *   link: string|null,
     *   overview: string|null,
     *   partner: array{name: string, website: string|null}|null,
     *   slug: string,
     *   startedAt: string|null,
     *   technologies: array<int, array{id: int, icon: string|null, name: string}>,
     *   title: string,
     *   website: string|null
     * }
     */
    public static function projectDetail(object $project): array
    {
        $gallery = [];

        if (isset($project->id)) {
            $gallery = array_map(
                static fn(object $media): array => [
                    'path' => (string) ($media->path ?? ''),
                ],
                MediaService::getFor('project', (int) $project->id)
            );
        }

        $partner = isset($project->partner) && is_object($project->partner)
            ? [
                'name' => (string) ($project->partner->name ?? ''),
                'website' => self::nullableString($project->partner->website ?? null),
            ]
            : null;

        return [
            'createdAt' => self::nullableString($project->created_at ?? null),
            'description' => self::nullableString($project->description ?? null),
            'endedAt' => self::nullableString($project->ended_at ?? null),
            'gallery' => $gallery,
            'id' => (int) ($project->id ?? 0),
            'img' => self::nullableString($project->img ?? null),
            'link' => self::nullableString($project->link ?? null),
            'overview' => self::nullableString($project->overview ?? null),
            'partner' => $partner,
            'slug' => self::projectSlug($project),
            'startedAt' => self::nullableString($project->started_at ?? null),
            'technologies' => method_exists($project, 'technologies')
                ? array_map([self::class, 'technology'], $project->technologies())
                : [],
            'title' => (string) ($project->title ?? ''),
            'website' => self::nullableString($project->website ?? null),
        ];
    }

    /**
     * @return array{
     *   createdAt: string|null,
     *   id: int,
     *   img: string|null,
     *   link: string|null,
     *   overview: string|null,
     *   slug: string,
     *   subtitle: string|null,
     *   tags: array<int, array{id: int, name: string, slug: string}>,
     *   title: string
     * }
     */
    public static function articleCard(object $article): array
    {
        return [
            'createdAt' => self::nullableString($article->created_at ?? null),
            'id' => (int) ($article->id ?? 0),
            'img' => self::nullableString($article->img ?? null),
            'link' => self::nullableString($article->link ?? null),
            'overview' => self::nullableString($article->overview ?? null),
            'slug' => self::articleSlug($article),
            'subtitle' => self::nullableString($article->subtitle ?? null),
            'tags' => self::articleTags((int) ($article->id ?? 0)),
            'title' => (string) ($article->title ?? ''),
        ];
    }

    /**
     * @return array{
     *   createdAt: string|null,
     *   id: int,
     *   img: string|null,
     *   link: string|null,
     *   overview: string|null,
     *   slug: string,
     *   subtitle: string|null,
     *   tags: array<int, array{id: int, name: string, slug: string}>,
     *   title: string
     * }
     */
    public static function articleDetail(object $article): array
    {
        return self::articleCard($article);
    }

    /**
     * @return array{
     *   currentPage: int,
     *   hasNext: bool,
     *   hasPages: bool,
     *   hasPrevious: bool,
     *   items: array<int, array{
     *     createdAt: string|null,
     *     id: int,
     *     img: string|null,
     *     link: string|null,
     *     overview: string|null,
     *     slug: string,
     *     subtitle: string|null,
     *     tags: array<int, array{id: int, name: string, slug: string}>,
     *     title: string
     *   }>,
     *   nextPage: int,
     *   pageRange: array<int>,
     *   perPage: int,
     *   previousPage: int,
     *   totalItems: int,
     *   totalPages: int
     * }
     */
    public static function pagination(PaginationResult $pagination): array
    {
        return [
            'currentPage' => $pagination->currentPage,
            'hasNext' => $pagination->hasNext(),
            'hasPages' => $pagination->hasPages(),
            'hasPrevious' => $pagination->hasPrevious(),
            'items' => array_map([self::class, 'articleCard'], $pagination->items),
            'nextPage' => $pagination->nextPage(),
            'pageRange' => $pagination->pageRange(),
            'perPage' => $pagination->perPage,
            'previousPage' => $pagination->previousPage(),
            'totalItems' => $pagination->totalItems,
            'totalPages' => $pagination->totalPages,
        ];
    }

    /**
     * @return array<int, array{id: int, name: string, slug: string}>
     */
    private static function articleTags(int $articleId): array
    {
        if ($articleId <= 0) {
            return [];
        }

        return array_map(
            static fn(object $tag): array => [
                'id' => (int) ($tag->id ?? 0),
                'name' => (string) ($tag->name ?? ''),
                'slug' => (string) ($tag->slug ?? ''),
            ],
            TagService::getForArticle($articleId)
        );
    }

    private static function projectSlug(object $project): string
    {
        $slug = self::nullableString($project->slug ?? null);

        return $slug ?? (string) ($project->id ?? '');
    }

    private static function articleSlug(object $article): string
    {
        $slug = self::nullableString($article->slug ?? null);

        return $slug ?? (string) ($article->id ?? '');
    }

    private static function nullableString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
