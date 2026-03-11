<?php

declare(strict_types=1);

namespace App\Model;


use App\Core\DataLayer\Model;


class Certificate extends Model
{
    protected string $table = 'corsi';
    protected int|string|null $id = null;
    protected ?string $title = null;
    protected ?string $overview = null;
    protected ?string $certified = null;
    protected ?string $link = null;
    protected ?string $ente = null;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;

    protected function casts(): array
    {
        return ['certified' => 'string'];
    }
}
