<?php

namespace App\Model;


use App\Core\DataLayer\Model;

class Skill extends Model
{

    protected string $table = 'skills';

    protected array $fillable = ['id', 'title', 'description'];

}
