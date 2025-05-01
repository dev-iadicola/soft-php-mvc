<?php

namespace App\Model;


use App\Core\Eloquent\ORM;
use App\Traits\BelongsTo;
use App\Traits\Getter;
use App\Traits\Has;
use App\Traits\Relation;

class Article extends ORM
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
