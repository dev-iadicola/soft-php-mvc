<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class ArticleTag extends Model
{
    protected string $table = 'article_tag';
    protected ?int $id = null;
    protected int $article_id;
    protected int $tag_id;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;
}
