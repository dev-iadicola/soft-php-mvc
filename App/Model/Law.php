<?php

namespace App\Model;


use App\Core\Eloquent\Model;
use App\Traits\Getter;

class Law extends Model
{
    use Getter;
    protected string $table = 'laws';

    protected array $fillable = ['id', 'title', 'testo'];

}
