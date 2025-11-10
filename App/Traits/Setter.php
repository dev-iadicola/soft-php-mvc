<?php

namespace App\Traits;

trait Setter
{
    public function __set($key, mixed $val)
    {




        // Setter personalizzato
        if (method_exists($this, $key)) {
            return $this->$key($val);
        }

        // Proprietà dichiarata nella classe
        if (property_exists($this, $key)) {
            $this->$key = $val;
            return;
        }
        // ! NOT CHANGE IT WORK
        $this->attributes[$key] = $val;
    }
}
