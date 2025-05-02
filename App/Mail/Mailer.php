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
    

    public function __construct(Mvc $mvc)
    {
        $this->mvc = $mvc;
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
        $mail = $this->mvc->Smtp->getMailer();
        $content = $this->content;

        
        
        

        try {
            
            $mail->setFrom($from, $fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $this->setPage($body, $content); 

            return $mail->send();
        } catch (ExceptionSMTP $e) {
            //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
        
    }

    public function setContent( $content = []){
       return  $this->content = $content;
    }

    private function getContent(){
        return $this->content;
    }

    public function setPage(string $page,  $content = []): string
    {
        $mail = $this->mvc->config['folder']['mails'] . '/' . $page.'.php';
        ob_start(); // start capturing output

    if (is_object($content)) {
        $content = get_object_vars($content);
    }

    if (!is_array($content)) {
        throw new \InvalidArgumentException('Content must be an array or an object');
    }

    extract($content); // extract variables from content array

        $ext = extract($content); // extract variables from content array
        
        include($mail); // execute the file
        $output = ob_get_contents(); // get the contents from the buffer
        ob_end_clean(); // stop buffering and discard contents

        return $output; // return the captured content
    }
}
