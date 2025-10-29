<?php 
namespace App\Core\Services;

use App\Core\Helpers\Log;
use App\Core\Contract\ITimeoutStrategy;

/**
 * Summary of SessionStorage
 * Questa classe rispetta il pattern singleton ha la responsabilità di gestire la sessione 
 * Viene implementato attualmente dalla clase AuthService
 */
class SessionStorage {

    
    private static ?SessionStorage $instance = null;
    private ITimeoutStrategy $inactivityTimeout;
    
    /**
     * Singleton Pattern
     */
    private function __construct(){
        // Imposta sicurezza prima dell'avvio della sessione. 
        ini_set('session.cookie_httponly',1); // il codice non è legginile da JS
        ini_set('session.use_strict_mode', 1); // PHP non accetta ID sessione non validi
       if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') {
            ini_set('session.cookie_secure', 1); // il cookie viaggia solo su HTTPS
        }
        $this->startSession();
    }

   


 // Impedisci clonazione e unserialize
    private function __clone(): void {}
    public function __wakeup(): void {}

     
    public static function getInstance() : SessionStorage{
        // Se è già stato inzializzato con get instance, prende instance altrimenti crea instanza privata
        if(self::$instance === null){
            return self::$instance = new SessionStorage();
        }
        return self::$instance;
    }


    /**
     * Summary of startSession
     * Fa partire la sessione, ritorna un booleano se viene attivata, altrimenti s'è già attiva tirona false.
     * @return bool
     */
    private function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
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
    public function verifyTimeFlashSession() {
        if (isset($_SESSION['TIME']) && (time() - $_SESSION['TIME']) > 0) {
            $this->flashSessionDestroy();
        }
    }

    /**
     * Summary of verifyInactivityTimeout
     * @deprecated  verrà sostituito dallo strategy InactivityTimeout
     * @return void
     */
    private function verifyInactivityTimeout(): void
    {
        $timeout = 600; // 10 minuti

        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
            $this->destroy();
            Log::info("Sessione scaduta per inattività.");
        }

        $_SESSION['LAST_ACTIVITY'] = time(); // aggiorna il timestamp
    }

    public function setOrCreate(int|string $key, int|float|string|array|bool|null $value): array {
      return  $_SESSION[$key] = $value;
    }

    public  function create(array $arraySession): array|null {
        
        foreach ($arraySession as $key => $value) {
            $_SESSION[$key] = $value;
        }
        return $this->getAll();
    }

    public function getAll(): array|null {
        return $_SESSION ?? null;
    }

    public  function get(string $key):mixed {
        return $_SESSION[$key] ?? null;
    }

    public function unset($key):array|null {
        
        unset($_SESSION[$key]);
        return $this->getAll() ?? null;
    }

    public function setFlashSession($key, $value) {
        $_SESSION['FLASH'][$key] = $value;
        $_SESSION['TIME'] = time(); // Imposta il tempo corrente
    }

    public function getFlashSession($key): array|null {
        
        $flash = $_SESSION['FLASH'][$key] ?? null;
        if ($flash !== null) {
            unset($_SESSION['FLASH'][$key]); // Rimuove il flash session dopo averlo letto
            if (empty($_SESSION['FLASH'])) {
                unset($_SESSION['FLASH']); // Rimuove la sezione FLASH se vuota
            }
        }
        return $flash;
    }

    private function flashSessionDestroy() {
        if (isset($_SESSION['FLASH'])) {
            $_SESSION['FLASH'] = ''; 
            unset($_SESSION['FLASH']);
            error_log("Sessione Flash distrutta.");
        }
    }


    public  function destroy(): int {
       
        $_SESSION = [];
        session_unset();
        session_destroy();
        Log::info("Session Destroy");
        return session_status();
    }
    
}
