<?php

declare(strict_types=1);

namespace App\Model;


use App\Core\DataLayer\Model;


class Article extends Model
{

    protected string $table = 'articles';
    protected int|string|null $id = null;
    protected ?string $title = null;
    protected ?string $subtitle = null;
    protected ?string $overview = null;
    protected ?string $img = null;
    protected ?string $link = null;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;


}
