<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Model\Law;

class LawRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Law::class);
    }
}
