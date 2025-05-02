<?php

namespace App\Model;


use App\Core\Eloquent\ORM;
use App\Traits\Getter;
use App\Traits\Relation;

class Certificate extends ORM
{
    use Getter; use Relation;
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
