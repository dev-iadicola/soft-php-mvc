<?php

namespace App\Model;


use App\Core\ORM;

class Curriculum extends ORM
{
    static string $table = 'curriculum';

    static array $fillable = ['id', 'title', 'img','download'];

    
}
