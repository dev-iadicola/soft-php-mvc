<?php

declare(strict_types=1);

namespace App\Model;


use App\Core\DataLayer\Model;


class Curriculum extends Model
{
    protected string $table = 'curriculum';
    protected ?int $id = null;
    protected string $title;
    protected string $img;
    protected int $download = 0;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;

    protected function casts(): array
    {
        return ['download' => 'int'];
    }
}
