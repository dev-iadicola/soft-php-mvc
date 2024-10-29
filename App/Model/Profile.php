<?php

namespace App\Model;


use App\Core\ORM;

class Profile extends ORM
{
    static string $table = 'profile';

    static array $fillable = ['id', 'name', 'tagline', 'welcome_message','selected'];

}
