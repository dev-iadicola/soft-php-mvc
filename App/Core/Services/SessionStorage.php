<?php

namespace App\Core\Services;

use App\Core\Helpers\Log;
use App\Core\Contract\ITimeoutStrategy;
use App\Traits\Attributes;
use App\Utils\Enviroment;

/**
 * Summary of SessionStorage
 * Questa classe rispetta il pattern singleton ha la responsabilità di gestire la sessione 
 * Viene implementato attualmente dalla clase AuthService e CsrfService
 */
class SessionStorage
{


    private static ?SessionStorage $instance = null;
    private ITimeoutStrategy $inactivityTimeout;

    private int $timeout; // set the last activity
    private int $lifetime; // set the life of the sessiuon

    /**
     * Singleton Pattern
     */
    private function __construct()
    {

        $this->setTimeout();

        //  Imposta sicurezza dei cookie
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') {
            ini_set('session.cookie_secure', 1);
        }

        //  Avvio della sesssione.
        $this->startSession();
    }




    public static function getInstance(): SessionStorage
    {
        // Se è già stato inzializzato con get instance, prende instance altrimenti crea instanza privata
        if (self::$instance === null) {
            return self::$instance = new SessionStorage();
        }
        return self::$instance;
    }




    // Impedisci clonazione e unserialize
    private function __clone(): void {}
    public function __wakeup(): void {}

    /**
     * Sets the maximum lifetime of the current session.
     * 
     * This method defines how long a session should remain valid,
     * both on the server (via `gc_maxlifetime`) and on the client
     * (via `session.cookie_lifetime`).
     * 
     * It also synchronizes the cookie parameters to ensure consistency
     * and security across HTTPS and HTTP environments.
     * 
     * ---
     * 
     * Imposta la durata massima della sessione corrente.
     * 
     * Questo metodo definisce per quanto tempo una sessione rimane valida,
     * sia lato server (tramite `gc_maxlifetime`) che lato client
     * (tramite `session.cookie_lifetime`).
     * 
     * Inoltre, sincronizza i parametri del cookie per garantire coerenza
     * e sicurezza sia in ambienti HTTPS che HTTP.
     * 
     * @param int|null $lifetime
     *     The lifetime of the session in seconds.  
     *     If null, uses the value defined in the configuration file.
     *     Durata della sessione in secondi.  
     *     Se nullo, utilizza il valore definito nel file di configurazione.
     * 
     * @return void
     * 
     * @see https://www.php.net/manual/en/session.configuration.php
     * @see https://owasp.org/www-project-cheat-sheets/cheatsheets/Session_Management_Cheat_Sheet.html
     */

    public function __get($key)
    {

        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        if (property_exists($this, $key)) {
            return $this->$key;
        }
        if (property_exists($this, $key)) {
            return $this->$key;
        }
        return $_SESSION[$key];
    }

    public function __set($key, mixed $val)
    {
        if (method_exists($this, $key)) {
            return $this->$key($val);
        }
        if (property_exists($this, $key)) {
            $this->$key = $val;
            return;
        }
        // ! NOT CHANGE IT WORK
        $_SESSION[$key] = $val;
    }

    // * Bisogna utilizzarlo solo quando l'utente effettua il login
    /**
     * Sets the session lifetime.
     * 
     * Should be called only when the user logs in,
     * to define how long the session remains valid.
     *
     * @param int|null $lifetime Session duration in seconds (default from config)
     * @return void
     */
    public function setLifeTime(?int $lifetime = null): void
    {
        // Se la sessione è già avviata, non modificare i parametri
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Se non è passato un valore, prendi quello dal file di configurazione
        $this->lifetime = $lifetime ?? (int) mvc()->config->settings["session"]["lifetime"];

        // Imposta la durata massima dei dati di sessione lato server
        ini_set('session.gc_maxlifetime', $this->lifetime);

        // Imposta la durata del cookie di sessione lato client
        ini_set('session.cookie_lifetime', $this->lifetime);

        // Reimposta i parametri del cookie in modo coerente
        session_set_cookie_params([
            'lifetime' => $this->lifetime,
            'httponly' => true,
            'secure' => isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on',
            'samesite' => 'Lax',
        ]);

        // Log utile per debug o monitoraggio

        Log::debug("Session lifetime set to {$this->lifetime} seconds.");
    }

    public function setTimeout(?int $time = null): void
    {
        $this->timeout = $time ?? (int) mvc()->config->settings["session"]["timeout"];
        Log::debug("Session timeout set to {$this->timeout} seconds.");
    }


    /**
     * Summary of startSession
     * Fa partire la sessione, ritorna un booleano se viene attivata, altrimenti s'è già attiva tirona false.
     * @return bool
     */
    private function startSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start(); // serve per riavviare la sessione.
            if (Enviroment::isDebug()) {
                Log::debug('session_start: ' . session_id());
                Log::debug($_SESSION);
            }

            return true;
        }

        return false;
    }

    /**
     * Summary of verifyTimeFlashSession
     * @return void
     * Rimuove la flash session (messaggi di successo, warning ed errore nel Frontend) dopo un breve periodo di tempo. 
     * todo: da creare una classe apposita per gestione la sessione della flash session/message
     */
    public function verifyTimeFlashSession(): void
    {
        if (!isset($_SESSION['FLASH_TIME'], $_SESSION['FLASH_TTL'])) {
            return;
        }

        $elapsed = time() - $_SESSION['FLASH_TIME'];
        if ($elapsed >= $_SESSION['FLASH_TTL']) {
            $this->flashSessionDestroy();
        }
    }

    public function set(string $key, mixed $value)
    {
        $_SESSION[$key]  = $value;
    }

    /**
     * Summary of verifyInactivityTimeout
     * @deprecated  verrà sostituito dallo strategy InactivityTimeout
     * @return void
     */
    private function verifyInactivityTimeout(): void
    {

        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $this->timeout) {
            $this->destroy();
            Log::info("Sessione scaduta per inattività.");
        }

        $_SESSION['LAST_ACTIVITY'] = time(); // aggiorna il timestamp
    }

    /**
     * GETTER , SETTER AND HAS
     *
     */


    public function setOrCreate(int|string $key, int|float|string|array|bool|null $value): mixed
    {
        return  $_SESSION[$key] = $value;
    }

    public  function create(array $arraySession): static
    {

        foreach ($arraySession as $key => $value) {
            $_SESSION[$key] = $value;
        }
        return $this;
    }

    public function getAll(): array|null
    {
        return $_SESSION ?? null;
    }

    public  function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
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
    public function getOrCreate(string $key, mixed $value): mixed
    {
        if (!$this->has($key)) {
            $this->create([$key => $value]);
        }
        return $this->get($key);
    }

    /**
     * Summary of has
     * Check if the key in session esist.
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function unset($key): array|null
    {

        unset($_SESSION[$key]);
        return $this->getAll() ?? null;
    }


    public function setFlashSession($key, $value, $ttl = 3)
    {
        $_SESSION['FLASH'][$key] = $value;
        $_SESSION['FLASH_TIME'] = time();
        $_SESSION['FLASH_TTL']  = $ttl; // default 3 secondi
    }

    /**
     * Legge e rimuove il flash (una sola volta)
     */
    public function getFlashSession($key): mixed
    {
        $flash = $_SESSION['FLASH'][$key] ?? null;
        Log::debug("Recupero flash session per chiave '{$key}': $flash ");
        if ($flash !== null) {
            unset($_SESSION['FLASH'][$key]);
            if (empty($_SESSION['FLASH'])) {
                unset($_SESSION['FLASH'], $_SESSION['FLASH_TIME'], $_SESSION['FLASH_TTL']);
            }
        }
        return $flash;
    }

    private function flashSessionDestroy(): void
    {
        unset($_SESSION['FLASH'], $_SESSION['FLASH_TIME'], $_SESSION['FLASH_TTL']);
        Log::info("Flash session distrutta.");
    }

    public  function destroy(): int
    {
        if (PHP_SESSION_ACTIVE == session_status()) {
            $_SESSION = [];
            session_unset();
            session_destroy();
            Log::info("Session Destroy:, id: " . session_id());
        }
        return session_status();
    }
}
