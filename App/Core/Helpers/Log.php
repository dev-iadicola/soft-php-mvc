<?php
namespace App\Core\Helpers;

class Log
{
    protected static string $logFile = __DIR__ . '/../../../storage/logs/info.log';

    public static function info( $message): void
    {
        self::writeLog('INFO', $message);
    }

    public static function error( $message): void
    {
        self::writeLog('ERROR', $message);
    }

    public static function debug(string $message): void
    {
        self::writeLog('DEBUG', $message);
    }

   
    protected static function writeLog(string $level,  $message = 'NA'): void
    {
        // Se è array o object, serializzo in JSON
        if (is_array($message) || is_object($message)) {
            $message = json_encode(
                $message, 
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
            );
        }

        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[{$date}] {$level}: {$message}\n";

        file_put_contents(self::$logFile, $formattedMessage, FILE_APPEND);
    }
}
