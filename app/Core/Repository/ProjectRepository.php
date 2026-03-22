<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Model\Project;

class ProjectRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Project::class);
    }
}
