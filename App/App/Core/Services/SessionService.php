<?php 
namespace App\Core\Services;

class SessionService {
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
            error_log("Sessione avviata.");
            return true;
        }
        error_log("Sessione già avviata.");
        return false;
    }
    

    public static function verifyTimeFlashSession() {
        self::startSession();
        if (isset($_SESSION['TIME']) && (time() - $_SESSION['TIME']) > 0) {
            self::flashSessionDestroy();
        }
    }

    public static function set($key, $value) {
        self::startSession();
        $_SESSION[$key] = $value;
        return self::getAll();
    }

    public static function create(array $arraySession) {
        self::startSession();
        foreach ($arraySession as $key => $value) {
            $_SESSION[$key] = $value;
        }
        return self::getAll();
    }

    public static function getAll() {
        self::startSession();
        return $_SESSION ?? null;
    }

    public static function get(string $key) {
        self::startSession();
        return $_SESSION[$key] ?? null;
    }

    public static function unset($key) {
        self::startSession();
        unset($_SESSION[$key]);
        return self::getAll() ?? null;
    }

    public static function setFlashSession($key, $value) {
        self::startSession();
        $_SESSION['FLASH'][$key] = $value;
        $_SESSION['TIME'] = time(); // Imposta il tempo corrente
        error_log("FLASH sessione impostata: " . print_r($_SESSION['FLASH'], true));
        error_log("Tempo TIME impostato: " . $_SESSION['TIME']);
    }

    public static function getFlashSession($key) {
        self::startSession();
        $flash = $_SESSION['FLASH'][$key] ?? null;
        if ($flash !== null) {
            unset($_SESSION['FLASH'][$key]); // Rimuove il flash session dopo averlo letto
            if (empty($_SESSION['FLASH'])) {
                unset($_SESSION['FLASH']); // Rimuove la sezione FLASH se vuota
            }
        }
        return $flash;
    }

    private static function flashSessionDestroy() {
        self::startSession();
        if (isset($_SESSION['FLASH'])) {
            $_SESSION['FLASH'] = ''; 
            unset($_SESSION['FLASH']);
            error_log("Sessione Flash distrutta.");
        }
    }


    public static function destroy() {
        self::startSession();
        $_SESSION = [];
        session_unset();
        session_destroy();
        error_log("Sessione completamente distrutta.");
        return session_status();
    }
    
}
