<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class Media extends Model
{
    protected string $table = 'media';
    protected ?int $id = null;
    protected string $entity_type;
    protected int $entity_id;
    protected string $path;
    protected string $disk = 'public';
    protected int $sort_order = 0;
    protected ?string $created_at = null;
}
