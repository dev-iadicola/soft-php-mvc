<?php

namespace App\Model;


use App\Model\Log;
use App\Traits\Getter;
use App\Core\Eloquent\ORM;
use App\Traits\Relation;

class User extends ORM
{
    use Getter; use Relation;

    protected string $table = 'users';
    protected array $fillable = [
        'email',
        'log_id',
        'password',
        'token',
        'indirizzo',
        'last_log'
    ];

    public static function changePassword(string $password, string $email)
    {
        $user =  User::where('email', $email)->first();
        if (empty($user)) {
            return false;
        }
        $password = password_hash($password, PASSWORD_BCRYPT);

        $user->update(['password' => $password]);


        return $user;
    }
    public function log(){
        return $this->hasMany(Log::class, 'log_id');
    }
}
