<?php

namespace App\Core\Helpers;

use Throwable;
use App\Mail\BrevoMail;
use App\Mail\BaseMail;
use App\Utils\Enviroment;

class Log
{
    // base path robusto
    protected static string $logFile = '';

    /** Inizializza il path del log una sola volta */
    protected static function init(): void
    {
        if (self::$logFile !== '') {
            return;
        }

        // /App/Core/Helpers -> salgo di 3 livelli fino alla root del progetto
        $base = dirname(__DIR__, 3);
        $dir  = $base . '/storage/logs';

        if (!is_dir($dir)) {
            // 0775 va bene su linux; su windows viene ignorato
            @mkdir($dir, 0775, true);
        }

        self::$logFile = $dir . '/app.log';
    }

    public static function info(mixed $message): void
    {
        self::writeLog('INFO', $message);
    }

    public static function error(mixed $message): void
    {
        self::writeLog('ERROR', $message);
    }

    public static function debug(mixed $message): void
    {
        // * the app in production dont write in file app.log.
        if(! Enviroment::isDebug()){
            return;
        }
        self::writeLog('DEBUG', $message);
    }

    public static function exception(Throwable $exception): void
    {
        $payload = [
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
            'code'    => $exception->getCode(),
            'trace'   => $exception->getTraceAsString(),
        ];

        self::writeLog('EXCEPTION', $payload);
    }

    /** Messaggio importante / violazione */
    public static function alert(string $message): void
    {
        self::writeLog('ALERT', "====================\n!!! {$message} !!!\n====================");
    }

    /**
     * Invia una mail e logga eventuali errori.
     * @return bool true se spedita, false se errore
     */
    public static function email(
        string $message,
        string $to,
        string $subject = '',
        string $page = 'standard',
        ?BaseMail $serviceSMTP = null,
    ): bool {
        try {
            $serviceSMTP ??= new BrevoMail();
            $serviceSMTP->bodyHtml($page, ['subject' => $subject]);
            $serviceSMTP->setEmail($to, $subject, ['message' => $message]);
            $serviceSMTP->send();

            self::info("Email inviata a {$to} con subject '{$subject}'");
            return true;
        } catch (Throwable $e) {
            self::exception($e);
            return false;
        }
    }

    /** Scrittura su file con fallback a error_log */
    protected static function writeLog(string $level, mixed $message = 'NA'): void
    {
        self::init();

        // Normalizza il messaggio
        if (is_array($message) || is_object($message)) {
            $message = json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // Timestamp locale Roma; se preferisci, impostalo nel bootstrap una sola volta
        date_default_timezone_set('Europe/Rome');
        $date = date('Y-m-d H:i:s');

        $line = "[{$level}] {$date} | {$message}\n";

        try {
            // LOCK_EX evita corruzione in scritture concorrenti
            $ok = @file_put_contents(self::$logFile, $line, FILE_APPEND | LOCK_EX);
            if ($ok === false) {
                // Fallback sul log di PHP
                error_log("LOG WRITE FAIL: " . $line);
            }
        } catch (Throwable $e) {
            // Ultimo fallback
            error_log("LOG EXCEPTION: " . $e->getMessage() . " | original: " . $line);
        }
    }
}
