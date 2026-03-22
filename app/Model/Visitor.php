<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class Visitor extends Model
{
    protected ?int $id = null;
    protected string $ip_address;
    protected ?string $user_agent = null;
    protected ?string $url = null;
    protected ?string $session_id = null;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;
}
