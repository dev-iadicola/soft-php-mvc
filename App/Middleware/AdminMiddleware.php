<?php 
namespace App\Middleware;

use App\Core\Mvc;
use App\Model\User;
use App\Core\Contract\MiddlewareInterface;
use App\Core\Services\AuthService;
use App\Core\Services\SessionService;

class AdminMiddleware implements MiddlewareInterface
{

    protected User $user;


    public function exec(Mvc $mvc)
    {
        $validAuth = $this->isAuthenticated() && $this->timerSession();

        

        if (!$validAuth) {
            SessionService::destroy();
            return $mvc->response->redirect('/login');
        }
        
    }   

  
    protected function isAuthenticated() {
        if (SessionService::get('AUTH_TOKEN')) {
            $token = SessionService::get('AUTH_TOKEN');
            return $this->verifyTokenInDatabase($token);
        }
        return false;
    }


    
    protected function verifyTokenInDatabase($token) {
        $user = User::where('token',$token)->first();
       return (empty($user))? false : true  ;
    }


    protected function timerSession() {
        // verifica se ha effettuato il login
    if ((!empty(SessionService::get('LOGGED_IN'))) && SessionService::get('LOGGED_IN')=== true) {
        // Validazione IP
        if (SessionService::get('IP') !== $_SERVER['REMOTE_ADDR']) {
            AuthService::logout();
            return false;
        }
        // Validazione timer della sessione
        $timeoutDuration = 1800; // 30 min
        if (time() - SessionService::get('LAST_PING') > $timeoutDuration) {
            AuthService::logout();
            return false;
        }
        SessionService::set('LAST_PING',time()); // Aggiorna 
        return true;
    }
    return false;
}
    

}