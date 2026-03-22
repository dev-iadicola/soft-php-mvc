<?php

declare(strict_types=1);

namespace App\Core\Facade;

use App\Core\Mvc;
use App\Core\DataLayer\Model;
use App\Core\Services\AuthService;
use App\Model\User;

/**
 * Summary of Auth: Facade + Singleton
 */
class Auth {
    protected static ?AuthService $instance = null;

    public static function getInstance(): AuthService{
        if(!self::$instance){
            self::$instance = new AuthService(Mvc::$mvc->sessionStorage);
        }
        return self::$instance;
    }

   /**
    * Utilizzalo per verificare che un utente abbia effettuato l'accesso
    */
   public static function check(): bool {
    return Auth::getInstance()->isLogged();
   }

   public static function login(User $user): bool
   {
    return Auth::getInstance()->login($user);
   }

   /**
    * @return Model|null
    */
   public static function user(): ?Model {
    return self::getInstance()->user();
   }

   /**
    * Summary of logout
    * Esegue il logout rimuovendo la sessione dell'utente
    * @return void
    */
   public static function logout(): void{
     Auth::getInstance()->logout();
   }


}
