<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class EmailTemplate extends Model
{
    protected string $table = 'email_templates';
    protected ?int $id = null;
    protected string $slug;
    protected string $subject;
    protected string $body;
    protected bool $is_active = true;
    protected ?string $updated_at = null;

    protected function casts(): array
    {
        return ['is_active' => 'bool'];
    }
}
