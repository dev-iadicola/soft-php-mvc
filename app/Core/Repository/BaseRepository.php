<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Core\DataLayer\Model;
use App\Core\DataLayer\Query\ActiveQuery;
use App\Core\Repository\RepositoryInterface;

abstract class BaseRepository implements RepositoryInterface
{
    /** @var class-string<Model> */
    protected string $modelClass;

    /**
     * @param class-string<Model> $modelClass
     */
    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function find(int|string $id): ?Model
    {
        return $this->query()->find($id);
    }

    public function all(): array
    {
        return $this->query()->all();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Model
    {
        return $this->query()->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int|string $id, array $data): bool
    {
        $model = $this->find($id);

        if ($model === null) {
            return false;
        }

        $pk = $model->getKeyId();

        return $this->query()->where($pk, $id)->update($data);
    }

    public function delete(int|string $id): bool
    {
        $model = $this->find($id);

        if ($model === null) {
            return false;
        }

        $pk = $model->getKeyId();

        return (bool) $this->query()->where($pk, $id)->delete();
    }

    protected function query(): ActiveQuery
    {
        return ($this->modelClass)::query();
    }

    /**
     * @return array<Model>
     */
    protected function where(string $column, mixed $conditionOrValue, mixed $value = null): array
    {
        return $this->query()->where($column, $conditionOrValue, $value)->get();
    }

    /**
     * @param array<string>|string $columns
     * @return array<Model>
     */
    protected function orderBy(array|string $columns, string $direction = 'ASC'): array
    {
        return $this->query()->orderBy($columns, $direction)->get();
    }

    protected function findOrFail(int|string $id): Model
    {
        return $this->query()->findOrFail($id);
    }
}
