<?php

namespace App\Model;


use App\Core\Eloquent\ORM;
use App\Traits\Getter;

class Law extends ORM
{
    use Getter;
    protected string $table = 'laws';

    protected array $fillable = ['id', 'title', 'testo'];

}
