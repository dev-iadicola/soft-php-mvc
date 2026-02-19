<?php

namespace App\Model;

use App\Model\LogTrace;
use App\Core\DataLayer\Model;
use App\Traits\Relation;

class User extends Model   
{
    protected string $table = 'users';
    protected array $fillable = [
        'email',
        'password',
        'token',
        'indirizzo',
        'last_log',
        'log_id',
        'crerated_at'
    ];

    public static function changePassword(string $password, string $email)
    {
        $user = User::query()->where('email', $email)->first();

        if (empty($user)) {
            return false;
        }
        $password = password_hash($password, PASSWORD_BCRYPT);
        User::query()->where('email', $email)->update(['password' => $password]);
        return $user;
    }

    
    // public function log(){
    //     return $this->hasMany(LogTrace::class, 'log_id');
    // }


}
