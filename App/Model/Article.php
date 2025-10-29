<?php

namespace App\Model;


use App\Core\Eloquent\Model;


class Article extends Model
{

    protected string $table = 'articles';

    protected array $fillable = [
        'id',
        'title',
        'subtitle',
        'overview',
        'img',
        'link',
        'created_at'
    ];

   
}
