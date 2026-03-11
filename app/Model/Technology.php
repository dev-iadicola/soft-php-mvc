<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class Technology extends Model{
    protected string $table = 'technology';
    protected ?int $id = null;
    protected string $name;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;
}
