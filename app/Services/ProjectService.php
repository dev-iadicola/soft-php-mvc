<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Helpers\Str;
use App\Model\Project;
use App\Core\Exception\NotFoundException;

class ProjectService
{
    /**
     * @return array<int, Project>
     */
    public static function getAll(string $orderBy = 'sort_order', string $order = 'ASC', ?string $technology = null): array
    {
        if ($technology !== null && $technology !== '') {
            /** @var array<int, Project> */
            return Project::query()
                ->distinct()
                ->select('projects.*')
                ->from('projects')
                ->join('project_technologies', 'project_technologies.project_id', '=', 'projects.id')
                ->join('technology', 'technology.id', '=', 'project_technologies.technology_id')
                ->where('technology.name', $technology)
                ->orderBy('projects.sort_order')
                ->get();
        }

        return Project::query()->orderBy($orderBy, $order)->get();
    }

    /**
     * @return array<int, Project>
     */
    public static function getActive(string $orderBy = 'sort_order', string $order = 'ASC', ?string $technology = null): array
    {
        if ($technology !== null && $technology !== '') {
            /** @var array<int, Project> */
            return Project::query()
                ->distinct()
                ->select('projects.*')
                ->from('projects')
                ->join('project_technologies', 'project_technologies.project_id', '=', 'projects.id')
                ->join('technology', 'technology.id', '=', 'project_technologies.technology_id')
                ->where('technology.name', $technology)
                ->where('projects.is_active', 1)
                ->orderBy('projects.sort_order')
                ->get();
        }

        return Project::query()->where('is_active', true)->orderBy($orderBy, $order)->get();
    }

    /**
     * @throws NotFoundException
     */
    public static function findOrFail(int $id): Project
    {
        $project = Project::query()->find($id);

        if ($project === null) {
            throw new NotFoundException("Project with id {$id} not found");
        }

        /** @var Project $project */
        return $project;
    }

    public static function findBySlug(string $slug): ?Project
    {
        /** @var Project|null */
        $project = Project::query()->where('slug', $slug)->first();

        // Fallback: cerca per titolo (compatibilita con URL pre-slug)
        if ($project === null) {
            $project = Project::query()->where('title', $slug)->first();
        }

        return $project;
    }

    public static function create(array $data): Project
    {
        if (!isset($data['slug']) && isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        /** @var Project */
        return Project::query()->create($data);
    }

    public static function update(int $id, array $data): bool
    {
        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return Project::query()->where('id', $id)->update($data);
    }

    public static function delete(int $id): void
    {
        Project::query()->where('id', $id)->delete();
    }
}
