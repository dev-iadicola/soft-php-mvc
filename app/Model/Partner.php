<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class Partner extends Model
{
    protected ?int $id = null;
    protected string $name;
    protected ?string $website = null;
    protected int $sort_order = 0;
    protected bool $is_active = true;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;

    protected function casts(): array
    {
        return ['is_active' => 'bool'];
    }
}
