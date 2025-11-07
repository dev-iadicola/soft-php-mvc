<?php

namespace App\Core\Services;

use App\Model\User;
use App\Model\LogTrace;
use App\Core\Helpers\Log;
use App\Core\Eloquent\Model;


class AuthService
{

    private SessionStorage $_sessionStorage;
    private ?Model $_user = null;
    public function __construct(SessionStorage $sessionStorage)
    {
        $this->_sessionStorage = $sessionStorage;
    }

    /**
     * Handles the user login process.
     * 
     * Generates a unique authentication token for the given user model,
     * persists it in the database, and initializes a new authenticated session.
     * Also logs the login event in the LogTrace table and sets the current user
     * instance in memory for subsequent authentication checks.
     * 
     * Gestisce il processo di login dell'utente.
     * 
     * Genera un token di autenticazione univoco per il modello utente fornito,
     * lo salva nel database e inizializza una nuova sessione autenticata.
     * Registra inoltre l'evento di accesso nella tabella LogTrace e imposta
     * l'istanza dell’utente corrente in memoria per i controlli futuri.
     * 
     * @param Model $model  The user model instance retrieved from the database.
     *                      Istanza del modello utente recuperata dal database.
     * 
     * @return bool True if login is successful and the session is initialized,
     *              false otherwise.
     *              True se il login è avvenuto con successo e la sessione è stata inizializzata,
     *              false altrimenti.
     */
    public function login(Model $model)
    {
        if ($model) {
            // Generate token from server and save the value
            $token = static::generateToken();

            $model->token = $token;
            // Save token in the database.
            $model->save();

            // Save the data of user and the token in session
            static::startUserSession(token: $token);

            // Save the user in tracelog.
            LogTrace::createLog($model->id);

            // Save the model
            $this->setUser($model);


            return true;
        }
        return false;
    }
    /**
     * Summary of logout
     * 
     * Elimina la sessione dell'utente
     * @return void
     */
    public function logout()
    {
        $this->_sessionStorage->destroy();
    }

    public function user(): ?Model
    {
        return $this->_user;
    }
    public function setUser(Model $model)
    {
        $this->_user = $model;
    }


    /**
     * Summary of checkIpAddressSessionAndRemoteAddr
     * Verifica se l'utente abbia la sessione con l'IP con il quale si sia registrato e abbia lo stesso durante l'attività server
     * @return bool
     */
    public function checkIpAddressSessionAndRemoteAddr(): bool
    {
        return $this->_sessionStorage->get('IP') === $_SERVER['REMOTE_ADDR'];
    }

    public function isLogged(): bool
    {
        $logged = $this->_sessionStorage->get("LOGGED_IN") || $this->_user != null;
        return !empty($logged) || $logged == true;
    }

    public function isAuthenticated(): bool
    {
        if ($this->_sessionStorage->get('AUTH_TOKEN')) {

            $token = $this->_sessionStorage->get('AUTH_TOKEN');
          
            return $this->verifyTokenInDatabase($token);
        }
        return false;
    }

    public function updateLastPing(int $value): void
    {
        $this->_sessionStorage->setOrCreate('LAST_PING', $value);
    }

    public function destroySession()
    {
        $this->_sessionStorage->destroy();
    }

    protected function verifyTokenInDatabase($token)
    {
        $user = User::where('token', $token)->first();
        return (empty($user)) ? false : true;
    }




    /**
     * Initializes a new authenticated user session.
     * Stores the authentication token and environment data (IP, User-Agent)
     * and applies the configured auth session lifetime.
     * 
     * Inizializza una nuova sessione utente autenticata.
     * Memorizza il token di autenticazione e i dati di ambiente (IP, User-Agent)
     * applicando la durata configurata per la sessione autenticata.
     */

    private function startUserSession($token): void
    {
        // setting the token, last ping, 
        $this->_sessionStorage->create([
            'AUTH_TOKEN' => $token,
            'LAST_PING' => time(),
            'LOGGED_IN' => TRUE,
            'IP' => $_SERVER['REMOTE_ADDR'],
            'DEVICE' =>  $_SERVER['HTTP_USER_AGENT'],
            'SESSION_CONTEXT' => 'auth'
        ]);
     
    }


    protected static function generateToken($length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}
