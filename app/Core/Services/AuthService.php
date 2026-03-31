<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\Helpers\Log;
use App\Core\DataLayer\Model;
use App\Model\AuthSession;
use App\Model\User;
use App\Services\AuthSessionService;


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
        $this->_sessionStorage->regenerateId();

        $sessionId = $this->_sessionStorage->id();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $device = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $model->setAttribute('token', $token);
        $model->save();

        $userId = (int) $model->getAttribute('id');

        AuthSessionService::create($sessionId, $userId, $ip, $device);

        // Save the data of user and the token in session
        $this->startUserSession(token: $token, sessionId: $sessionId, userId: $userId);

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
        $sessionId = (string) $this->_sessionStorage->get('AUTH_SESSION_ID', $this->_sessionStorage->id());

        if ($sessionId !== '') {
            AuthSessionService::terminate($sessionId);
        }

        $this->_user = null;
        $this->_sessionStorage->destroy();
    }

    public function user(): ?Model
    {
        if ($this->_user !== null) {
            return $this->_user;
        }

        $userId = (int) $this->_sessionStorage->get('AUTH_USER_ID', 0);

        if ($userId <= 0) {
            return null;
        }

        /** @var User|null $user */
        $user = User::query()->find($userId);
        if ($user instanceof User) {
            $this->setUser($user);
        }

        return $user;
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
        return $this->_sessionStorage->get('IP') === ($_SERVER['REMOTE_ADDR'] ?? '');
    }

    public function isLogged(): bool
    {
        return (bool) $this->_sessionStorage->get("LOGGED_IN") || $this->user() !== null;
    }

    public function isAuthenticated(): bool
    {
        $sessionId = (string) $this->_sessionStorage->get('AUTH_SESSION_ID', $this->_sessionStorage->id());
        $userId = (int) $this->_sessionStorage->get('AUTH_USER_ID', 0);

        if ($sessionId === '' || $userId <= 0 || !$this->isLogged()) {
            return false;
        }

        $session = $this->currentAuthSession();

        return $session instanceof AuthSession && (int) $session->getAttribute('user_id') === $userId;
    }

    public function updateLastPing(int $value): void
    {
        $this->_sessionStorage->setOrCreate('LAST_PING', $value);

        $sessionId = (string) $this->_sessionStorage->get('AUTH_SESSION_ID', $this->_sessionStorage->id());
        if ($sessionId !== '') {
            AuthSessionService::touch($sessionId);
        }
    }

    public function destroySession(): void
    {
        $this->logout();
    }

    public function currentAuthSession(): ?AuthSession
    {
        $sessionId = (string) $this->_sessionStorage->get('AUTH_SESSION_ID', $this->_sessionStorage->id());

        if ($sessionId === '') {
            return null;
        }

        return AuthSessionService::find($sessionId);
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

    private function startUserSession(string $token, string $sessionId, int $userId): void
    {
        // setting the token, last ping,
        $this->_sessionStorage->create([
            'AUTH_TOKEN' => $token,
            'AUTH_USER_ID' => $userId,
            'AUTH_SESSION_ID' => $sessionId,
            'LAST_PING' => time(),
            'LOGGED_IN' => true,
            'IP' => $_SERVER['REMOTE_ADDR'] ?? '',
            'DEVICE' =>  $_SERVER['HTTP_USER_AGENT'] ?? '',
            'SESSION_CONTEXT' => 'auth'
        ]);

    }


    protected static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}
