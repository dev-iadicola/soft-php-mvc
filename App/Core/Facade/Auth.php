<?php 
namespace App\Core\Facade;

use App\Core\Eloquent\Model;
use App\Core\Services\AuthService;
use App\Core\Eloquent\QueryBuilder;
use App\Core\Services\SessionStorage;

/**
 * Summary of Auth: Facade + Singleton
 */
class Auth {
    protected static ?AuthService $instance = null;

    protected static function getInstance(): AuthService{
        if(!self::$instance){
            self::$instance = new AuthService(SessionStorage::getInstance());
        }
        return self::$instance;
    }

   /**
    * Summary of check
    * Utilizzalo per verificare che un utente abbia effettuato l'accesso
    * @return void
    */
   public static function check(): bool {
    return Auth::getInstance()->isLogged();
   }

   public static function login(Model $user){
    return Auth::getInstance()->login($user); 
   }

   /**
    * Summary of user
    * 
    * @return QueryBuilder|null ritrna l'istanza del queryBuilder quindi è possibile effetuare 
    * subito Auth::user()->id o altri campi. 
    */
   public static function user(): QueryBuilder|null{
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