<?php

declare(strict_types=1);

/**
 * Summary of namespace Session
 * Facade session, simply use for yout framewrok!
 */
namespace App\Core\Facade;

use App\Core\Mvc;
use App\Core\Services\SessionStorage;

class Session {

    public static function self(): SessionStorage{
        return Mvc::$mvc->sessionStorage;
    }
    public static function setFlash(string $key, mixed $val): void{
       Mvc::$mvc->sessionStorage->setFlashSession($key,$val); 
    }

    public static function getFlash(string $key): string|null{
       return Mvc::$mvc->sessionStorage->getFlashSession($key);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return Mvc::$mvc->sessionStorage->get($key, $default);
    }

    /**
     * Summary of hasOrCreate
     * Allows you to check whether a key exist in the current session.
     * 
     * - if exist, return true
     * - else if it donesn't exist, 
     *   it take the array|string $value and insert into the session and save it with 
     *   key $key value your use for search a value in array.
     *    
     * @param string $key
     * @param array|string $value
     * @return array|bool|null
     */
    public static function getOrCreate(string $key, mixed $values): mixed{
        return Mvc::$mvc->sessionStorage->getOrCreate($key, $values);
    }
    public static function has(string $key): bool{
        return Mvc::$mvc->sessionStorage->has($key);
    }

    public static function create(array $array): SessionStorage{
        return Mvc::$mvc->sessionStorage->create($array);
    }

    public static function set(string $key, mixed $value): void
    {
        Mvc::$mvc->sessionStorage->set($key, $value);
    }

    public static function remove(string $key): void
    {
        Mvc::$mvc->sessionStorage->remove($key);
    }

    public static function removeMany(array $keys): void
    {
        Mvc::$mvc->sessionStorage->removeMany($keys);
    }

    public static function flash(string $key, mixed $value): void
    {
        Mvc::$mvc->sessionStorage->flash($key, $value);
    }

    public static function getFlashedErrors(): array
    {
        return Mvc::$mvc->sessionStorage->getFlashedErrors();
    }

    public static function flashErrors(array $errors): void
    {
        Mvc::$mvc->sessionStorage->flashErrors($errors);
    }

    public static function flashOldInput(array $data): void
    {
        Mvc::$mvc->sessionStorage->flashOldInput($data);
    }

    public static function getOldInput(string $key, mixed $default = null): mixed
    {
        return Mvc::$mvc->sessionStorage->getOldInput($key, $default);
    }
}
