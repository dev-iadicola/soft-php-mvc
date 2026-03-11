<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Model\Certificate;

class CertificateRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Certificate::class);
    }
}
