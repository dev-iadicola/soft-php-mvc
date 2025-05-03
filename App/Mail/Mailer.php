<?php

namespace App\Mail;

use App\Core\Connection\SMTP;
use App\Core\Contract\MailBaseInterface;
use App\Core\Mvc;
use PHPMailer\PHPMailer\Exception as ExceptionSMTP;

class Mailer extends BaseMail implements MailBaseInterface
{
    private Mvc $mvc;

    private  $content = [];

    public function __construct()
    {
        parent::__construct();
    }



    public function sendEmail(
        string $to,
        string $subject,
        string $body,
        string $from = NULL,
        string $fromName = NULL,
    ) {

        $from ??= getenv('APP_EMAIL');
        $fromName ??= getenv('APP_NAME');
        $mail = smtp()->getMailer();
        $content = $this->content;





        try {

            $mail->setFrom($from, $fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $this->bodyHtml($body, $content);

            return $mail->send();
        } catch (ExceptionSMTP $e) {
            //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }

    public function setContent($content = [])
    {
        return  $this->content = $content;
    }
}
