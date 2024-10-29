<?php

namespace App\Model;


use App\Core\ORM;

class Article extends ORM
{
    static string $table = 'articles';

    static array $fillable = ['id', 'title', 'subtitle','overview','img','link','created_at'];

}
