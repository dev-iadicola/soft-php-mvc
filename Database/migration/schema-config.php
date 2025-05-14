<?php 

use App\Core\Eloquent\Schema\SchemaBuilder;
use App\Model\User;

$schema = new SchemaBuilder();
return [
App\Core\Migration::schema($schema),
User::schema($schema),
];


