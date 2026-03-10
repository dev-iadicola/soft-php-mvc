<?php

namespace App\Core;
use App\Core\Contract\ModelSchemaInterface;
use App\Core\DataLayer\Model;
use App\Core\DataLayer\Schema\SchemaBuilder;

class Migration extends Model implements ModelSchemaInterface
{
  
    protected string $table = 'migrations';
    protected ?string $migration_table = null;
    protected ?string $json_sql = null;
    protected ?string $created = null;
    protected ?string $updated = null;

    public static function schema(SchemaBuilder $schema)
    {
        return $schema->table('migrations')
            ->stringId('table')
            ->json('json_sql')
            ->timestamps()
            ->build();
    }

    protected function columnMap(): array
    {
        return ['migration_table' => 'table'];
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
