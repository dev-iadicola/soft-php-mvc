<?php

namespace App\Middleware;

use App\Core\Mvc;
use App\Model\User;
use App\Core\Services\AuthService;
use App\Core\Services\SessionStorage;
use App\Core\Contract\ITimeoutStrategy;
use App\Core\Strategy\InactivityTimeout;
use App\Core\Contract\MiddlewareInterface;
use App\Core\Http\Request;

class AuthMiddleware implements MiddlewareInterface
{

    private AuthService $_authService;

    private ITimeoutStrategy $_itimeout_strategy;


    /**
     * Executes the authentication middleware.
     * 
     * This middleware validates the current user's authentication and session integrity.
     * It ensures that:
     *  - The user is authenticated and has a valid active session.
     *  - The session has not expired according to the configured inactivity timeout strategy.
     *  - The IP address matches the one used at login (prevents session hijacking).
     * 
     * If any of these checks fail, the session is destroyed and the user
     * is redirected to the login page.
     * 
     * ---
     * 
     * Esegue il middleware di autenticazione.
     * 
     * Questo middleware valida l’autenticazione e l’integrità della sessione corrente.
     * Garantisce che:
     *  - L’utente sia autenticato e disponga di una sessione valida e attiva.
     *  - La sessione non sia scaduta secondo la strategia di timeout configurata.
     *  - L’indirizzo IP corrisponda a quello utilizzato durante il login (evita furti di sessione).
     * 
     * Se uno di questi controlli fallisce, la sessione viene distrutta
     * e l’utente viene reindirizzato alla pagina di login.
     * 
     * @param Request $request
     *     The current HTTP request instance.
     *     Istanza corrente della richiesta HTTP.
     * 
     * @return \App\Core\Http\Response|null
     *     Redirects to the login page if the session is invalid,
     *     otherwise allows the request to continue.
     *     Reindirizza alla pagina di login se la sessione è invalida,
     *     altrimenti consente di proseguire con la richiesta.
     */

    public function exec(Request $request)
    {
        $this->_authService = new AuthService(SessionStorage::getInstance());

        $this->_itimeout_strategy = new InactivityTimeout(mvc()->config->settings["session"]["timeout"]);

        $validAuth = $this->_authService->isAuthenticated() && $this->checkSession($this->_itimeout_strategy);

        if (!$validAuth) {
            $this->_authService->destroySession();

            return mvc()->response->redirect('/login');
        }
    }


    /**
     * Validates the current session using the given inactivity timeout strategy.
     * 
     * Checks:
     *  - If the user is logged in.
     *  - If the IP address is consistent with the one stored at login.
     *  - If the session has not expired due to inactivity.
     * 
     * Aggiorna il timestamp dell’ultima attività in caso di sessione valida.
     * 
     * @param InactivityTimeout $strategy The timeout validation strategy.
     * @return bool True if the session is valid, false otherwise.
     */

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
