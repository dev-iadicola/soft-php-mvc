<?php

declare(strict_types=1);

namespace App\Model;


use App\Core\DataLayer\Model;

class Law extends Model
{

    protected string $table = 'laws';
    protected int|string|null $id = null;
    protected ?string $title = null;
    protected ?string $testo = null;

}
