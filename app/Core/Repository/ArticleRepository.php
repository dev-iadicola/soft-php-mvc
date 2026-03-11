<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Model\Article;

class ArticleRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Article::class);
    }
}
