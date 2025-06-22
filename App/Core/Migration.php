<?php

namespace App\Core;
use App\Core\Contract\ModelSchemaInterface;
use App\Traits\Getter;
use App\Core\Eloquent\Model;
use App\Core\Eloquent\Schema\SchemaBuilder;
use App\Traits\Relation;

class Migration extends Model implements ModelSchemaInterface
{
    use Getter;
    use Relation;
    protected string $table = 'migrations';
    protected array $fillable = [
        'table',
        'json_sql',
        'created',
        'updated',
    ];

    public static function schema(SchemaBuilder $schema)
    {
        return $schema->table('migrations')
            ->stringId('table')
            ->json('json_sql')
            ->timestamps()
            ->build();
    }

    public static function setMigration(string $table, array $columns)
    {  if(! $table == 'table' ){
        $jsonColumns = json_encode($columns, JSON_UNESCAPED_UNICODE);
        return self::save(['table' => $table, 'json_sql' => $jsonColumns]);
    }
        
    }

    public static function getMigration(string $table, array $columns)
    {
        return json_decode(self::find($table)->json_sql);
    }



}
