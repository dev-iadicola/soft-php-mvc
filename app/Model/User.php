<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\LogTrace;
use App\Core\DataLayer\Model;

class User extends Model
{
    protected ?int $id = null;
    protected ?int $log_id = null;
    protected string $password;
    protected string $email;
    protected ?string $token = null;
    protected ?string $two_factor_secret = null;
    protected bool $two_factor_enabled = false;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;

    protected function casts(): array
    {
        return [
            'two_factor_enabled' => 'bool',
        ];
    }

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
