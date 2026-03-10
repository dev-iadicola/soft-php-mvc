<?php

namespace App\Model;


use App\Core\DataLayer\Model;


class Certificate extends Model
{
    protected string $table = 'corsi';
    protected int|string|null $id = null;
    protected ?string $title = null;
    protected ?string $overview = null;
    protected ?int $certified = null;
    protected ?string $link = null;
    protected ?string $ente = null;

    protected function casts(): array
    {
        return ['certified' => 'int'];
    }
}
