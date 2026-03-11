<?php

declare(strict_types=1);

namespace App\Model;


use App\Core\DataLayer\Model;

class Skill extends Model
{

    protected string $table = 'skills';
    protected int|string|null $id = null;
    protected ?string $title = null;
    protected ?string $description = null;

}
