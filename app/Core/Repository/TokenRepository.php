<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Model\Token;

class TokenRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Token::class);
    }
}
