<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class Notification extends Model
{
    protected string $table = 'notifications';
    protected ?int $id = null;
    protected string $type;
    protected string $title;
    protected ?string $message = null;
    protected ?string $link = null;
    protected bool $is_read = false;
    protected ?string $created_at = null;

    protected function casts(): array
    {
        return ['is_read' => 'bool'];
    }
}
