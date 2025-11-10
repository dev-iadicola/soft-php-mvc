<?php

namespace App\Core\Helpers;

use App\Core\Storage;
use Throwable;
use App\Mail\BrevoMail;
use App\Mail\BaseMail;
use App\Utils\Date;
use App\Utils\Enviroment;

class Log
{
    // base path robusto
    protected static string $logFile = '';
    private Storage $storage;


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
        if (! Enviroment::isDebug()) {
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

            // Se è un oggetto non JsonSerializable → converti in array di proprietà accessibili
            if (is_object($message) && !($message instanceof \JsonSerializable)) {
                $message = get_object_vars($message);
            }
    
            $encoded = json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
    
            if ($encoded === false) {
                $encoded = print_r($message, true);
            }
    
            $message = $encoded;
        }

        // Timestamp  Roma  //Todo: verra' messo quello orginale 
        date_default_timezone_set('Europe/Rome');
        $date = Date::Rome('Y-m-d H:i:s');

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


    /**
     * Normalizza strutture complesse per il log:
     * - Limita la profondità per evitare dump enormi
     * - Previene ricorsioni cicliche
     * - Converte Traversable e oggetti in array semplici (quando possibile)
     * - Pulisce stringhe non-UTF8
     */
    private static function normalize(mixed $value, int $depth = 3, null|\SplObjectStorage $seen = null): mixed
    {
        if ($depth < 0) {
            return '*DEPTH*';
        }

        if ($seen === null) {
            $seen = new \SplObjectStorage();
        }

        if ($value instanceof Throwable) {
            // riusa stringify per Throwable
            return self::stringify($value);
        }

        if ($value instanceof \Stringable) {
            return (string)$value;
        }

        if (is_object($value)) {
            if ($seen->contains($value)) {
                return '*RECURSION*';
            }
            $seen->attach($value);

            // Traversable → array
            if ($value instanceof \Traversable) {
                $tmp = [];
                foreach ($value as $k => $v) {
                    $tmp[$k] = self::normalize($v, $depth - 1, $seen);
                }
                return $tmp;
            }

            // Prova con get_object_vars; se non accessibile, usa il nome classe
            $props = @get_object_vars($value);
            if ($props === null) {
                // ultimo tentativo per oggetti opachi
                return $value::class;
            }

            $out = ['__class' => $value::class];
            foreach ($props as $k => $v) {
                $out[$k] = self::normalize($v, $depth - 1, $seen);
            }
            return $out;
        }

        if (is_array($value)) {
            $out = [];
            foreach ($value as $k => $v) {
                $out[$k] = self::normalize($v, $depth - 1, $seen);
            }
            return $out;
        }

        // Pulisci stringhe non-UTF8 per evitare errori di json_encode
        if (is_string($value)) {
            if (!mb_check_encoding($value, 'UTF-8')) {
                $value = mb_convert_encoding($value, 'UTF-8', 'auto');
            }
        }

        return $value;
    }

    /**
     * Converte qualunque valore in stringa loggabile:
     * - Throwable: messaggio + file/line + trace
     * - Stringable / __toString: usa cast
     * - Array/Object: normalizza con limite di profondità e anti-ricorsione
     * - Risorse/Closure: descrizione testuale
     * - Fallback: print_r
     */
    private static function stringify(mixed $value): string
    {
        if ($value instanceof Throwable) {
            return sprintf(
                '%s: %s (code: %s) in %s:%d%sStack:%s%s',
                $value::class,
                $value->getMessage(),
                (string)$value->getCode(),
                $value->getFile(),
                $value->getLine(),
                PHP_EOL,
                PHP_EOL,
                $value->getTraceAsString()
            );
        }

        if ($value instanceof \Stringable) {
            return (string) $value;
        }

        // Evita notice se l'oggetto ha __toString
        if (is_object($value) && method_exists($value, '__toString')) {
            try {
                return (string)$value;
            } catch (Throwable) { /* continua */
            }
        }

        if (is_array($value) || is_object($value)) {
            $normalized = self::normalize($value, 3); // profondità max=3 (modifica a piacere)
            $json = json_encode(
                $normalized,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            );

            if ($json !== false) {
                return $json;
            }

            // Fallback se json_encode fallisce (UTF-8 sporco, ecc.)
            return print_r($normalized, true);
        }

        if (is_resource($value)) {
            return 'resource(' . get_resource_type($value) . ')';
        }

        // bool/null/numeric/string
        if (is_bool($value))   return $value ? 'true' : 'false';
        if ($value === null)   return 'null';

        return (string)$value;
    }
}
