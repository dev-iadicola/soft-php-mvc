<?php

declare(strict_types=1);

namespace App\Mail;

use App\Core\Connection\SMTP;
use App\Core\Contract\MailBaseInterface;
use App\Core\GetEnv;
use RuntimeException;
use PHPMailer\PHPMailer\Exception as ExceptionSMTP;

class Mailer extends BaseMail implements MailBaseInterface
{
    private array $content = [];

    public function sendEmail(
        string $to,
        string $subject,
        string $body,
        string $from = NULL,
        string $fromName = NULL,
    ): bool {

        $from ??= GetEnv::requiredString('APP_EMAIL');
        $fromName ??= GetEnv::requiredString('APP_NAME');
        try {
            $mail = smtp()->getMailer();
            $content = $this->content;

            $mail->setFrom($from, $fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $this->bodyHtml($body, $content);

            return $mail->send();
        } catch (ExceptionSMTP|RuntimeException $e) {
            //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }

    public function setContent(array $content = []): array
    {
        return $this->content = $content;
    }
}
