<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Model\Partner;

class PartnerRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Partner::class);
    }
}
