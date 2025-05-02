<?php

namespace App\Model;


use App\Core\Eloquent\ORM;
use App\Traits\Getter;
use App\Traits\Relation;

class Profile extends ORM
{
    use Getter; use Relation;
    protected string $table = 'profile';

    protected array $fillable = [
        'id',
        'name',
        'tagline',
        'welcome_message',
        'selected'
    ];


}
