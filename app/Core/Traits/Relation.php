<?php

declare(strict_types=1);

namespace App\Core\Traits;

trait Relation {


  
    public function hasOne(string $model, string $foreignKey, ?string $localKey = null): mixed {
        $localKey = $localKey ?: $this->getKeyId();
        return $model::query()->where($foreignKey, $this->{$localKey})->first();
    }

    public function hasMany(string $model, string $foreignKey = '', ?string $localKey = null): mixed {
        $localKey = $localKey ?: $this->getKeyId();
        return $model::query()->where($foreignKey, $this->{$localKey})->get();
    }

    public function belongsTo(string $model, ?string $foreignKey = null, ?string $localKey = null): mixed {
        $foreignKey = $foreignKey ?: $this->getKeyId();
        return $model::query()->where($localKey ?: 'id', $this->{$foreignKey})->first();
    }
    public function belongsToMany(string $related, string $table, string $foreignKey, string $relatedKey): mixed {
        return $related::query()->join($table, $table . '.' . $foreignKey, '=', $this->getTable() . '.' . $relatedKey)
            ->where($this->getTable() . '.' . $this->getKeyId(), $this->{$this->getKeyId()})
            ->get();
    }
    public function belongsToManyThrough(string $related, string $through, string $firstKey, string $secondKey): mixed {
        return $related::query()->join($through, $through . '.' . $firstKey, '=', $this->getTable() . '.' . $secondKey)
            ->where($this->getTable() . '.' . $this->getKeyId(), $this->{$this->getKeyId()})
            ->get();
    }
    
}
