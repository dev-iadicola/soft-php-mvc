<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Model\User;
use App\Model\LogTrace;
use App\Core\Helpers\Log;
use App\Core\DataLayer\Model;


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
     * @param User $model  The user model instance retrieved from the database.
     *
     * @return bool True if login is successful and the session is initialized,
     *              false otherwise.
     */
    public function login(User $model): bool
    {
        // Generate token from server and save the value
        $token = static::generateToken();
        // * Set lifetime of the session.
        $this->_sessionStorage->setLifeTime(mvc()->config->get('settings.session.auth-lifetime'));

        $model->setAttribute('token', $token);
        // Save token in the database.
        $model->save();

        // Save the data of user and the token in session
        $this->startUserSession(token: $token);

        // Save the model
        $this->setUser($model);

        return true;
    }
    /**
     * Summary of logout
     *
     * Elimina la sessione dell'utente
     * @return void
     */
    public function logout(): void
    {
        $this->_sessionStorage->destroy();
    }

    public function user(): ?Model
    {
        return $this->_user;
    }
    public function setUser(Model $model): void
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
        return (bool) $this->_sessionStorage->get("LOGGED_IN") || $this->_user !== null;
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

    public function destroySession(): void
    {
        $this->_sessionStorage->destroy();
    }

    protected function verifyTokenInDatabase(string $token): bool
    {
        $user = User::query()->where('token', $token)->first();
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

    private function startUserSession(string $token): void
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


    protected static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}
