<?php  
namespace App\Core\Traits;

trait Relation {


  
    public function hasOne($model, $foreignKey, $localKey = null) {
        $localKey = $localKey ?: $this->getKeyId();
        return $model::query()->where($foreignKey, $this->{$localKey})->first();
    }

    public function hasMany($model, $foreignKey = '', $localKey = null) {
        $localKey = $localKey ?: $this->getKeyId();
        return $model::query()->where($foreignKey, $this->{$localKey})->get();
    }

    public function belongsTo($model, $foreignKey = null, $localKey = null) {
        $localKey = $localKey ?: $this->getKeyId();
        return $model::query()->where($foreignKey, $localKey)->first();
    }
    public function belongsToMany($related, $table, $foreignKey, $relatedKey) {
        return $related::query()->join($table, $table . '.' . $foreignKey, '=', $this->getTable() . '.' . $relatedKey)
            ->where($this->getTable() . '.' . $this->getKeyId(), $this->{$this->getKeyId()})
            ->get();
    }
    public function belongsToManyThrough($related, $through, $firstKey, $secondKey) {
        return $related::query()->join($through, $through . '.' . $firstKey, '=', $this->getTable() . '.' . $secondKey)
            ->where($this->getTable() . '.' . $this->getKeyId(), $this->{$this->getKeyId()})
            ->get();
    }
    
}