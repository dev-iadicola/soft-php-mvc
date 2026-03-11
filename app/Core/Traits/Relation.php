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

    public function belongsToMany(string $related, string $table, string $foreignKey, string $relatedKey): Collection|array {
        return $related::query()->join($table, $table . '.' . $foreignKey, '=', $this->getTable() . '.' . $relatedKey)
            ->where($this->getTable() . '.' . $this->getKeyId(), $this->getAttribute($this->getKeyId()))
            ->get();
    }

    public function belongsToManyThrough(string $related, string $through, string $firstKey, string $secondKey): Collection|array {
        return $related::query()->join($through, $through . '.' . $firstKey, '=', $this->getTable() . '.' . $secondKey)
            ->where($this->getTable() . '.' . $this->getKeyId(), $this->getAttribute($this->getKeyId()))
            ->get();
    }

}
