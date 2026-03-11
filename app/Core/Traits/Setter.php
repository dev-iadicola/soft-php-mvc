<?php

declare(strict_types=1);

namespace App\Core\Traits;

trait Setter
{
    public function __set(string $key, mixed $val): void
    {




        // Setter personalizzato
        if (method_exists($this, $key)) {
            $this->$key($val);
            return;
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
