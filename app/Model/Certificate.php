<?php

declare(strict_types=1);

namespace App\Model;


use App\Core\DataLayer\Model;


class Certificate extends Model
{
    protected string $table = 'corsi';
    protected ?int $id = null;
    protected string $title;
    protected ?string $overview = null;
    protected string $certified;
    protected string $link;
    protected string $ente;
    protected bool $is_active = true;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;

    protected function casts(): array
    {
        return ['certified' => 'string', 'is_active' => 'bool'];
    }
}
