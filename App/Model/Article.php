<?php

namespace App\Model;


use App\Core\Eloquent\Model;
use App\Traits\Getter;
use App\Traits\Relation;

class Article extends Model
{
    use Getter;
    use Relation;
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
