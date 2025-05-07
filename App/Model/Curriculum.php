<?php

namespace App\Model;


use App\Core\Eloquent\Model;
use App\Traits\Getter;
use App\Traits\Relation;

class Curriculum extends Model
{
    use Getter; use Relation;
    protected string $table = 'curriculum';

    protected array $fillable = ['id', 'title', 'img','download'];

    
}
