<?php

namespace App\Model;


use App\Core\ORM;

class Law extends ORM
{
    static string $table = 'laws';

    static array $fillable = ['id', 'title', 'testo'];

}
