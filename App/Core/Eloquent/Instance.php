<?php
namespace App\Core\Eloquent;

class Instance
{
    static function one(AbstractBuilder $builder): ?object
    {
        
        if (empty($builder)) {
            return null;
        }

        $model = new ($builder->modelName)(); // es. App\Models\User
        $model->setPDO($builder->pdo);
        $model->setFillable($builder->fillable);
        $model->setTable($builder->table);
        $model->setClassModel($builder->modelName);

        foreach ($builder as $key => $value) {
            $model->$key = $value;
        }

        return $model;
    }
    static function get(array $builder): array{
        $results = [];
        foreach ($builder as $row) {
            if($builder instanceof AbstractBuilder ){
                $model = new $builder->modelName;
                $builder->setPDO($builder->pdo);
                $builder->setFillable($builder->fillable);
                $builder->setTable($builder->table);
                $builder->setClassModel($builder->modelName);
                foreach ($row as $key => $value) {
                    $builder->$key = $value;
                }
                $results[] = $builder;
            }
           
        }
        return $results;
    }
}
