<?php 
namespace App\Core\Connection;

use PHPMailer\PHPMailer\PHPMailer;

class SMTP {
    public PHPMailer $mail;

    public function __construct(){
     
        $this->mail = new PHPMailer(true);

        //$this->mail->SMTPDebug  = 1; 
        $this->mail->isSMTP();

        $this->mail->Host         = getenv('SMTP_HOST');
        $this->mail->SMTPAuth     = getenv('SMTP_AUTH');
        $this->mail->Username     = getenv('SMTP_USERNAME');
        $this->mail->Password     = getenv('SMTP_PASSWORD');
        $this->mail->SMTPSecure   = getenv('SMTP_SECURE');
        $this->mail->Port         = getenv('SMTP_PORT');


    }

    public function getMailer(): PHPMailer {
        return $this->mail;
    }
}