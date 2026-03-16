<?php

declare(strict_types=1);

namespace App\Services;

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
            return Project::query()->query(
                'SELECT DISTINCT projects.*
                 FROM projects
                 INNER JOIN project_technologies ON project_technologies.project_id = projects.id
                 INNER JOIN technology ON technology.id = project_technologies.technology_id
                 WHERE technology.name = :technology
                 ORDER BY projects.sort_order ASC',
                [':technology' => $technology]
            );
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
            return Project::query()->query(
                'SELECT DISTINCT projects.*
                 FROM projects
                 INNER JOIN project_technologies ON project_technologies.project_id = projects.id
                 INNER JOIN technology ON technology.id = project_technologies.technology_id
                 WHERE technology.name = :technology AND projects.is_active = 1
                 ORDER BY projects.sort_order ASC',
                [':technology' => $technology]
            );
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
        return Project::query()->where('title', $slug)->first();
    }

    public static function create(array $data): Project
    {
        /** @var Project */
        return Project::query()->create($data);
    }

    public static function update(int $id, array $data): bool
    {
        return Project::query()->where('id', $id)->update($data);
    }

    public static function delete(int $id): void
    {
        Project::query()->where('id', $id)->delete();
    }
}
