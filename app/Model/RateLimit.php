<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class RateLimit extends Model
{
    protected string $table = 'rate_limits';
    protected bool $timestamps = false;
    protected ?int $id = null;
    protected string $ip;
    protected string $route;
    protected int $attempts = 0;
    protected string $last_attempt_at;

    protected function casts(): array
    {
        return [
            'id' => 'int',
            'attempts' => 'int',
        ];
    }
}
