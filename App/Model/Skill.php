<?php

namespace App\Model;


use App\Core\Eloquent\ORM;
use App\Traits\Getter;

class Skill extends ORM
{
    use Getter;
    protected string $table = 'skills';

    protected array $fillable = ['id', 'title', 'description'];

}
