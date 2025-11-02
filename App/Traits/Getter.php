<?php

namespace App\Traits;

trait Getter
{
    public function __get($key)
    {
        // Se esiste nell'array $attributes
        if (array_key_exists($key, $this->attributes ?? [])) {
            return $this->attributes[$key];
        }

        // chekc se essiste come prorp
        if (property_exists($this, $key)) {
            return $this->$key;
        }
        // check essite come attr
        if (property_exists($this, $key)) {
            return $this->$key;
        }

        return $this->attributes[$key];

        // $class = static::class;
        // throw new \Exception("Undefined property: {$class}::\${$key}");
    }
}
