<?php

namespace App\Model;


use App\Core\DataLayer\Model;


class Curriculum extends Model
{
    protected string $table = 'curriculum';
    protected int|string|null $id = null;
    protected ?string $title = null;
    protected ?string $img = null;
    protected ?int $download = null;

    protected function casts(): array
    {
        return ['download' => 'int'];
    }
}
