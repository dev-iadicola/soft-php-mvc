<?php
namespace App\Core\Eloquent;

class Instance
{
    private static ?AbstractBuilder $abstractBuilder = null;
    public static function context(AbstractBuilder $builder){
        self::$abstractBuilder = $builder;
    }
    static function one(array|string $data): ?object
    {
        
        if (empty($builder)) {
            return null;
        }

        self::$abstractBuilder;

        $model = new ($builder->classModel)(); // es. App\Models\User
        $model->setPDO($builder->pdo);
        $model->setFillable($builder->fillable);
        $model->setTable($builder->table);
        $model->setClassModel($builder->classModel);

        foreach ($builder as $key => $value) {
            $model->$key = $value;
        }

        return $model;
    }
    static function get(array $builder): array{
        $results = [];
        foreach ($builder as $row) {
            if($builder instanceof AbstractBuilder ){
                $model = new $builder->classModel;
                $model->setPDO($builder->pdo);
                $builder->setFillable($builder->fillable);
                $builder->setTable($builder->table);
                $builder->setClassModel($builder->classModel);
                foreach ($row as $key => $value) {
                    $builder->$key = $value;
                }
                $results[] = $builder;
            }
           
        }
        return $results;
    }
}
