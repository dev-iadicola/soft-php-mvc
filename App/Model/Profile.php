<?php

namespace App\Model;


use App\Core\DataLayer\Model;

class Profile extends Model
{
    protected string $table = 'profile';

    protected array $fillable = [
        'id',
        'name',
        'tagline',
        'welcome_message',
        'selected'
    ];


}
