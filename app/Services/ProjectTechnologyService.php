<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\ProjectTechnology;
use App\Model\Technology;

class ProjectTechnologyService
{
    /**
     * @return array<int, Technology>
     */
    public static function getByProject(int $projectId): array
    {
        /** @var array<int, Technology> */
        return Technology::query()
            ->select('technology.*')
            ->from('technology')
            ->join('project_technologies', 'project_technologies.technology_id', '=', 'technology.id')
            ->where('project_technologies.project_id', $projectId)
            ->orderBy(['technology.sort_order', 'technology.name'])
            ->get();
    }

    /**
     * @param array<int|string> $technologyIds
     */
    public static function syncForProject(int $projectId, array $technologyIds): void
    {
        $uniqueIds = array_values(array_unique(array_map(
            static fn (int|string $id): int => (int) $id,
            array_filter($technologyIds, static fn (mixed $id): bool => (int) $id > 0)
        )));

        ProjectTechnology::query()
            ->where('project_id', $projectId)
            ->delete();

        foreach ($uniqueIds as $technologyId) {
            ProjectTechnology::query()->query(
                'INSERT INTO project_technologies (project_id, technology_id) VALUES (:project_id, :technology_id)',
                [':project_id' => (string) $projectId, ':technology_id' => (string) $technologyId]
            );
        }
    }
}
