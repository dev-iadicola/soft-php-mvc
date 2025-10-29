<?php

namespace App\Model;


use App\Core\Eloquent\Model;


class Certificate extends Model
{
    protected string $table = 'corsi';

    protected  array $fillable = [
        'id',
        'title',
        'overview',
        'certified',
        'link',
        'ente'
    ];
}
