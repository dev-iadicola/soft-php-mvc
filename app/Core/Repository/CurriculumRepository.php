<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Model\Curriculum;

class CurriculumRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Curriculum::class);
    }
}
