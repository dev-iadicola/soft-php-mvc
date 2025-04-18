<?php

namespace App\Model;


use App\Core\Eloquent\ORM;
use App\Traits\BelongsTo;
use App\Traits\Getter;
use App\Traits\Has;
use App\Traits\Relation;

class Curriculum extends ORM
{
    use Getter; use Relation;
    protected string $table = 'curriculum';

    protected array $fillable = ['id', 'title', 'img','download'];

    
}
