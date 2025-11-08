<?php
/**
 * Summary of namespace Session
 * Facade session, simply use for yout framewrok!
 */
namespace App\Core\Facade; 
use App\Core\Services\SessionStorage;

class Session {

    public static function setFlash(string $key, mixed $val): void{
       mvc()->sessionStorage->setFlashSession($key,$val); 
    }

    public static function getFlash(string $key): string{
       return mvc()->sessionStorage->getFlashSession($key);
    }

    public static function get($key){
        return mvc()->sessionStorage->get($key);
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
        return mvc()->sessionStorage->getOrCreate($key, $values);
    }
    public static function has(string $key): bool{
        return mvc()->sessionStorage->has($key);
    }

    public static function create(array $array): SessionStorage{
        return mvc()->sessionStorage->create($array);
    }

    public static function set(string $key, mixed $value){
        return mvc()->sessionStorage->set($key, $value);
    }



    

}