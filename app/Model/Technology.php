<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class Technology extends Model{
    protected string $table = 'technology';
    protected int|string|null $id = null;
    protected ?string $name = null;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;
}
