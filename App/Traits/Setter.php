<?php

namespace App\Traits;

trait Setter
{
    public function __set($key, mixed $val)
    {
        // todo: decidere se sarà funzionale al sistema
        if (method_exists($this, $key)) {
            return $this->$key($val);
        }
        // ritorna valore dentro attributes 
        return $this->attributes[$key] = $val;
    }
}
