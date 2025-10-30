<?php
/**
 * Summary of namespace Session
 * Facade session, simply use for yout framewrok!
 */
namespace App\Core\Facade; 
use App\Core\Services\SessionStorage;

class Session {

    public static function setFlash(string $key, mixed $val){
        SessionStorage::getInstance()->setFlashSession($key, $val);
    }

    public static function getFlash(string $key): string{
        return SessionStorage::getInstance()->getFlashSession($key);
    }
    

}