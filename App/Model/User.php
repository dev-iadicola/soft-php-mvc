<?php

namespace App\Model;


use App\Core\Eloquent\ORM;
use App\Traits\Getter;

class User extends ORM
{
    use Getter;
    public protected string $table = 'users';

    protected array $fillable = [
        'email', 'password','token','indirizzo', 'last_log'
    ];

    public static function changePassword(string $password, string $email)
    {
        $user =  User::where('email', $email)->first();
        if(empty($user)){
            return false;
        }
        $password = password_hash($password, PASSWORD_BCRYPT);

        $user->update(['password' => $password]);

    
        return $user;
    }

   
}
