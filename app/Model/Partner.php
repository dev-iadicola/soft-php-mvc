<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class Partner extends Model
{

    protected string $table = 'partners';
    protected int|string|null $id = null;
    protected ?string $name = null;
    protected ?string $website = null;
}
