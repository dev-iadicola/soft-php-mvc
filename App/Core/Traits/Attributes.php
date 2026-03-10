<?php

namespace App\Core\Traits;

/**
 * Summary of Attributes
 * 
 * this trait provides the frameworks class with dinamic mechanism,
 * for managing proprieties via the associative `$attributes` array. 
 * 
 * It allows you to access and modify class fields
 * as if they where real proprieties of the class, while keeping then internally encapsulated  in the attributes array. 
 * 
 * @example $user = new User(); $user->name = 'John', and you call method save() this will be saved in database.
 * 
 * This system is useful for: 
 * managing field from the database without defining them as class propriety.
 * support custom getter and setter without breaking dynamic access; 
 * increment quality with serialization system
 * 
 * @package App\Traits
 * 
 */
trait Attributes
{
    protected array $attributes = [];
        
    public function __get($key)
    {
        // Declared typed properties take priority over the dynamic $attributes bag.
        if (method_exists($this, 'getAttribute')) {
            return $this->getAttribute($key);
        }

        return $this->attributes[$key] ?? null;
    }

    public function __set($key, mixed $val)
    {
        // Setter personalizzato
        if (method_exists($this, $key)) {
            return $this->$key($val);
        }

        if (method_exists($this, 'setAttribute')) {
            $this->setAttribute($key, $val);
            return;
        }

        $this->attributes[$key] = $val;
    }
}
