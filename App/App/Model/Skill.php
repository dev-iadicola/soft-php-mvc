<?php

namespace App\Model;


use App\Core\ORM;

class Skill extends ORM
{
    static string $table = 'skills';

    static array $fillable = ['id', 'title', 'description'];

}
