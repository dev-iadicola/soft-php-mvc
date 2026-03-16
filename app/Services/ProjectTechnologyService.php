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
        return Technology::query()->query(
            'SELECT technology.* 
             FROM technology
             INNER JOIN project_technologies ON project_technologies.technology_id = technology.id
             WHERE project_technologies.project_id = :project_id
             ORDER BY technology.name ASC',
            [':project_id' => (string) $projectId]
        );
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

        ProjectTechnology::query()->query(
            'DELETE FROM project_technologies WHERE project_id = :project_id',
            [':project_id' => (string) $projectId]
        );

        foreach ($uniqueIds as $technologyId) {
            ProjectTechnology::query()->query(
                'INSERT INTO project_technologies (project_id, technology_id) VALUES (:project_id, :technology_id)',
                [
                    ':project_id' => (string) $projectId,
                    ':technology_id' => (string) $technologyId,
                ]
            );
        }
    }
}
