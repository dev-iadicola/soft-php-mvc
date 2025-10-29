<?php

namespace App\Traits;

trait Getter
{
    public function __get($key)
    {

        // Altrimenti, accesso normale alla proprietà
        
        return $this->attributes[$key];
    }
}
