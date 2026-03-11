<?php

declare(strict_types=1);

namespace App\Model;


use App\Core\DataLayer\Model;

class Profile extends Model
{
    protected string $table = 'profile';
    protected int|string|null $id = null;
    protected ?string $name = null;
    protected ?string $tagline = null;
    protected ?string $welcome_message = null;
    protected ?bool $selected = null;

    protected function casts(): array
    {
        return ['selected' => 'bool'];
    }
}
