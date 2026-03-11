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
    public static function getAll(string $orderBy = 'id', string $order = 'DESC'): array
    {
        return Project::query()->orderBy($orderBy, $order)->get();
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
