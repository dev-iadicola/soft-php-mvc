<?php 
use App\Core\Eloquent\Schema\SchemaBuilder;

$schema = new SchemaBuilder();

$schema->table("tokens")
->stringId("token")
->string("email",60)->unique()
->bool('used')->default(true)
->timestamps()
->datetime('expiry_date')
->build();
// 1. Crea prima la tabella users
$schema->table('users')
    ->id()
    ->string('email',60)
    ->string('password')->notnull()
    ->string('token')
    ->string('indirizzo')
    ->string('last_log')
    ->timestamps()
    ->unique('email')
    ->build();

// 2. Poi crea la tabella logs
$schema->table('logs')
    ->id()
    ->integer("user_id")->unsigned()
    ->datetime('last_log')->notNull()
    ->string('address')->notNull()
    ->string('device')
    ->foreignKey('user_id', 'users' )
    ->build();
