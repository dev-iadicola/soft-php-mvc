<?php

namespace App\Model;

use App\Core\Contract\ModelSchemaInterface;
use App\Model\Log;
use App\Traits\Getter;
use App\Core\Eloquent\Model;
use App\Core\Eloquent\Schema\SchemaBuilder;
use App\Traits\Relation;

class User extends Model implements ModelSchemaInterface  
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

    public static function schema(SchemaBuilder $schema): void{
        $schema->table(self::$table)
            ->id()
            ->string('email')
            ->integer('log_id')
            ->string('password')
            ->string('token')
            ->string('indirizzo')
            ->string('last_log')
            ->timestamps()
            ->foreignKey('log_id', 'log_id', 'logs')
            ->unique('email')
            ->create();
    }

    public static function changePassword(string $password, string $email)
    {
        $user = User::where('email', $email)->first();
       
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
