<?php
 namespace App\Traits;

 trait Getter {
    public function __get($key)
    {
        // Se esiste un metodo, assumiamo che sia una relazione (es: $project->partner)
        if (method_exists($this, $key)) {
            return $this->$key();
        }

        // Altrimenti, accesso normale alla proprietà
        if (property_exists($this, $key)) {
            return $this->$key;
        }

        return null;
    }
 }