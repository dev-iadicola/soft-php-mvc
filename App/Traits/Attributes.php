<?php

namespace App\Traits;

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
    protected array $attributes;
        
    public function __get($key)
    {
        // Se esiste nell'array $attributes
        if (array_key_exists($key, $this->attributes)) {
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

        return $this->attributes[$key] ?? null;

        // $class = static::class;
        // throw new \Exception("Undefined property: {$class}::\${$key}");
    }

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
