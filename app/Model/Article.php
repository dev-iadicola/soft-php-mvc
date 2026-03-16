<?php

declare(strict_types=1);

namespace App\Model;


use App\Core\DataLayer\Model;


class Article extends Model
{
    protected ?int $id = null;
    protected string $title;
    protected string $subtitle;
    protected ?string $overview = null;
    protected ?string $img = null;
    protected ?string $link = null;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;


}
