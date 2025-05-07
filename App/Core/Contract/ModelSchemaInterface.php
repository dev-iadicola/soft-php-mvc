<?php 
namespace App\Core\Contract;

use App\Core\Eloquent\Schema\SchemaBuilder;

interface ModelSchemaInterface {
    public static function schema(SchemaBuilder $schema):void;
}