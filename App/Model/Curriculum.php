<?php

namespace App\Model;


use App\Core\Eloquent\Model;


class Curriculum extends Model
{
    protected string $table = 'curriculum';

    protected array $fillable = ['id', 'title', 'img','download'];

    
}
