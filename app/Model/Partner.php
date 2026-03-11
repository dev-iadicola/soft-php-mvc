<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class Partner extends Model
{

    protected string $table = 'partners';
    protected ?int $id = null;
    protected string $name;
    protected ?string $website = null;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;
}
