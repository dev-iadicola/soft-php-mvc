<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Model\Technology;

class TechnologyRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Technology::class);
    }
}
