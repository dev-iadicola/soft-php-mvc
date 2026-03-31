<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class Tag extends Model
{
    protected string $table = 'tags';
    protected ?int $id = null;
    protected string $name;
    protected string $slug;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;
}
