<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\LogTrace;
use App\Core\DataLayer\Model;

class User extends Model
{
    protected string $table = 'users';
    protected int|string|null $id = null;
    protected int|string|null $log_id = null;
    protected ?string $password = null;
    protected ?string $email = null;
    protected ?string $token = null;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;

    /**
     * @deprecated Use \App\Services\PasswordService::changeByEmail() instead.
     */
    public static function changePassword(string $password, string $email): static|false
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
