<?php
namespace App\Core\Helpers;

class Log
{
    protected static string $logFile = __DIR__ . '/../../../storage/logs/info.log';

    public static function info(string $message): void
    {
        self::writeLog('INFO', $message);
    }

    public static function error(string $message): void
    {
        self::writeLog('ERROR', $message);
    }

    public static function debug(string $message): void
    {
        self::writeLog('DEBUG', $message);
    }

    protected static function writeLog(string $level, string $message = ''): void
    {
        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[{$date}] {$level}: {$message}\n";

        file_put_contents(self::$logFile, $formattedMessage, FILE_APPEND);
    }
}
