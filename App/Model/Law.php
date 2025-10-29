<?php

namespace App\Model;


use App\Core\Eloquent\Model;

class Law extends Model
{

    protected string $table = 'laws';

    protected array $fillable = ['id', 'title', 'testo'];

}
