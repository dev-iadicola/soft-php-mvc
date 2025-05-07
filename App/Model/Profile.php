<?php

namespace App\Model;


use App\Core\Eloquent\Model;
use App\Traits\Getter;
use App\Traits\Relation;

class Profile extends Model
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
