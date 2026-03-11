<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Model\LogTrace;

class LogRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(LogTrace::class);
    }
}
