<?php

declare(strict_types=1);

namespace App\Core\Traits;

use App\Core\DataLayer\Query\ActiveQuery;
use App\Core\Support\Collection;

trait Relation {



    public function hasOne(string $model, string $foreignKey, ?string $localKey = null): mixed {
        $localKey = $localKey ?: $this->getKeyId();
        return $model::query()->where($foreignKey, $this->getAttribute($localKey))->first();
    }

    public function hasMany(string $model, string $foreignKey = '', ?string $localKey = null): Collection|array {
        $localKey = $localKey ?: $this->getKeyId();
        return $model::query()->where($foreignKey, $this->getAttribute($localKey))->get();
    }

    public function belongsTo(string $model, ?string $foreignKey = null, ?string $localKey = null): mixed {
        $foreignKey = $foreignKey ?: $this->getKeyId();
        return $model::query()->where($localKey ?: 'id', $this->getAttribute($foreignKey))->first();
    }

    /**
     * Many-to-many relation through a pivot table.
     *
     * @param string $related      Related model class
     * @param string $pivotTable   Pivot table name (e.g. 'project_technologies')
     * @param string $foreignPivotKey  Pivot column pointing to this model (e.g. 'project_id')
     * @param string $relatedPivotKey  Pivot column pointing to the related model (e.g. 'technology_id')
     */
    public function belongsToMany(string $related, string $pivotTable, string $foreignPivotKey, string $relatedPivotKey): Collection|array {
        $relatedTable = (new $related())->getTable();

        return $related::query()
            ->join($pivotTable, $pivotTable . '.' . $relatedPivotKey, '=', $relatedTable . '.id')
            ->where($pivotTable . '.' . $foreignPivotKey, $this->getAttribute($this->getKeyId()))
            ->get();
    }

    /**
     * Many-to-many relation through an intermediate table.
     *
     * @param string $related     Related model class
     * @param string $through     Intermediate table name
     * @param string $firstKey    Intermediate column pointing to this model
     * @param string $secondKey   Intermediate column pointing to the related model
     */
    public function belongsToManyThrough(string $related, string $through, string $firstKey, string $secondKey): Collection|array {
        $relatedTable = (new $related())->getTable();

        return $related::query()
            ->join($through, $through . '.' . $secondKey, '=', $relatedTable . '.id')
            ->where($through . '.' . $firstKey, $this->getAttribute($this->getKeyId()))
            ->get();
    }

}
