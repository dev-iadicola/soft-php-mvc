<?php

namespace App\Middleware;

use App\Core\Mvc;
use App\Model\User;
use App\Core\Services\AuthService;
use App\Core\Services\SessionStorage;
use App\Core\Contract\ITimeoutStrategy;
use App\Core\Strategy\InactivityTimeout;
use App\Core\Contract\MiddlewareInterface;


class AdminMiddleware implements MiddlewareInterface
{

    protected User $user;

    private AuthService $_authService;

    private ITimeoutStrategy $_itimeout_strategy;


    public function exec(?Mvc $mvc = null)
    {
        $this->_authService = new AuthService(SessionStorage::getInstance());
        // dall'ultima attività devono pssare 30 minuti.
        // * perché strategy? 
        /**
         * * Perché strategy?
         * Se in futuro volessimo implementare pià timeout secondo la tipologia di utenti, 
         * sarò possibile farlo tramite il file config
         * esempio array config[timeout_session][user_role]['admin'] return int 800;
         * esempio array config[timeout_session]['user_role']['cliente'] return int 1800;
         * esempio array config[timeout_session]['user_role']['1'] return int 1800;
         * si cicla su questo array, secondo la tipologia dell'utente Auth()->user()->role return 'admin' oppure 1
         * se scelgie il tipo di check da svolgere
         * 
         */
        $this->_itimeout_strategy = new InactivityTimeout(1800);

        $validAuth = $this->_authService->isAuthenticated() && $this->checkSession($this->_itimeout_strategy);

        if (!$validAuth) {
            $this->_authService->destroySession();
          
            return $mvc->response->redirect('/login');
        }
    }



    protected function checkSession(InactivityTimeout $strategy)
    {
        // verifica se ha effettuato il login
        if ($this->_authService->isLogged()) {
            // Validazione IP
           
            if (! $this->_authService->checkIpAddressSessionAndRemoteAddr()) {
                $this->_authService->logout();
                return false;
            }
            // Validazione timer della sessione : timeout scaduto secondo lo stratefgy scelto : logout
            // durata dal 'last_pint' è di 30 minuti (come indicato dal costruttore di questa classe)
            if ($strategy->IsExpired()) {
                $this->_authService->logout();
                return false;
            }
            $this->_authService->updateLastPing(time()); // Aggiorna 
            return true;
        }
        return false;
    }
}
