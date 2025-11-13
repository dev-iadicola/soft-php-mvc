<?php

namespace App\Core\DataLayer\Query;

use App\Core\Contract\QueryBuilderInterface;
use App\Core\DataLayer\Model;
use App\Core\Exception\ModelStructureException;
use InvalidArgumentException;

class ModelHydrator
{
    private string $name;
    private Model $model;

    public function __construct(private QueryBuilderInterface $queryBuilder)
    {

    }
    // Ceate a instance of Model. Dinamically!
    public function setModelClass(string $class): void
    {
        if (!class_exists($class)) {
            throw new ModelStructureException("Model class '{$class}' does not exist.");
        }
        // * instrance here
        $model = new $class();

        if (!$model instanceof Model) {
            throw new ModelStructureException("The class '{$class}' must extend App\\Core\\DataLayer\\Model.");
        }
        // * Assinged
        $this->name = $class;
        $this->model = $model;
    }

    /**
     * Summary of one
     * @param array<string,mixed>|bool $row (result of the query)
     * @return Model|null return Model hydratate with new proprieties
     */
    public function one(array|bool $row = []): Model|null
    {   if( $row === FALSE || empty($row))
            return null;
        
        /**
         * Why we clone (or create a new instance)
         * Each database row must correspond to a unique Model object in memory.
         * If we reused the same instance for all rows, each hydration would overwrite
         * the previous data, and every record would end up pointing to the same object.
         *
         * Using `clone $this->prototype` keeps the model’s preconfigured metadata
         * (like $table, $fillable, etc.), while ensuring every hydrated model
         * is an independent object.
         */
        $model = $this->model ? clone $this->model : new $this->name;
        $model->setQueryBuilder($this->queryBuilder);
        foreach ($row as $key => $value) {
            $model->setAttribute($key, $value);
        }
        return $model;
    }

    /**
     * Summary of many: Hydratate many models.
     * @param array $rows (result of query)
     * @return array<Model|null>
     */
    public function many(array $rows): array
    {
        $models = [];
        if (empty($rows)) {
            return $models;
        }

        foreach ($rows as $row) {
            $models[] = $this->one($row);
        }

        return $models;
    }

    /**
     * Summary of getModel
     * get the User or Post and any models.
     * @return Model
     */
    public function getModel(): Model{
        return $this->model;
    }

    public function __tostring(): string{
        return $this->name;
    }

   

}
