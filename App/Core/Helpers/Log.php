<?php

namespace App\Core\Helpers;

use Throwable;
use App\Model\User;
use App\Mail\BrevoMail;
use App\Core\Contract\MailBaseInterface;
use App\Mail\BaseMail;

class Log
{
    protected static string $logFile = __DIR__ . '/../../../storage/logs/app.log';

    public static function info($message): void
    {
        self::writeLog('INFO', $message);
    }

    public static function exception(Throwable $exception): void
    {
        $arrExc = [
            'Message'   => $exception->getMessage(),
            'File'      => $exception->getFile(),
            'Line'      => $exception->getLine(),
            'Code'      => $exception->getCode(),
            'Trace'     => $exception->getTraceAsString(),
        ];

        $strExc = "";
        foreach ($arrExc as $key => $value) {
            $strExc .= " {$key}: {$value} ";
        }
        $strExc .= ".";

        // Scrittura nel log (ipotizzando che writeLog accetti messaggio e testo)
        self::writeLog('Exception', $strExc);
    }

    // * Alert, important message (violation systme or other)
    public static function Alert(string $message)
    {
        self::writeLog("ALERT⚠️", "====================\n!!! $message  !!!\n====================");
    }


    public static function Email(
        string $message,
        string $to,
        string $subject = "",
        string $page = "standard",
        BaseMail $servceSMTP = new BrevoMail(),
    ) {
        $servceSMTP->bodyHtml($page, ["subject" => $subject,]);
        $servceSMTP->setEmail($to, $subject, ["message" => $message]);
        $servceSMTP->send();
    }

    public static function error($message): void
    {
        self::writeLog('ERROR', $message);
    }

    public static function debug($message): void
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

        date_default_timezone_set('Europe/Rome');
        $date = date('Y-m-d H:i:s');

        $formattedMessage = "[{$level}]: {$date} | {$message}\n";

        file_put_contents(self::$logFile, $formattedMessage, FILE_APPEND);
    }
}
