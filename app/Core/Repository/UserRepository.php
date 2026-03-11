<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Model\User;

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(User::class);
    }
}
