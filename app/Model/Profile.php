<?php

declare(strict_types=1);

namespace App\Model;


use App\Core\DataLayer\Model;

class Profile extends Model
{
    protected string $table = 'profile';
    protected ?int $id = null;
    protected string $name;
    protected ?string $tagline = null;
    protected ?string $welcome_message = null;
    protected ?string $bio = null;
    protected bool $selected = true;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;

    protected function casts(): array
    {
        return ['selected' => 'bool'];
    }
}
