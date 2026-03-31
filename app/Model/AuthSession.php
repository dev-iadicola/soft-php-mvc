<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class AuthSession extends Model
{
    protected string $table = 'sessions';
    protected bool $timestamps = false;
    protected ?string $id = null;
    protected int $user_id;
    protected string $ip;
    protected ?string $user_agent = null;
    protected string $last_activity;
    protected ?string $created_at = null;

    protected function casts(): array
    {
        return [
            'user_id' => 'int',
        ];
    }
}
