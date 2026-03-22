<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Model\Skill;

class SkillRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Skill::class);
    }
}
