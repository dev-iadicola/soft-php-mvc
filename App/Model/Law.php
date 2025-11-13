<?php

namespace App\Model;


use App\Core\DataLayer\Model;

class Law extends Model
{

    protected string $table = 'laws';

    protected array $fillable = ['id', 'title', 'testo'];

}
