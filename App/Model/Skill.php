<?php

namespace App\Model;


use App\Core\Eloquent\Model;
use App\Traits\Getter;

class Skill extends Model
{
    use Getter;
    protected string $table = 'skills';

    protected array $fillable = ['id', 'title', 'description'];

}
