<?php

declare(strict_types=1);

namespace App\Core\Connection;

use App\Core\GetEnv;
use PHPMailer\PHPMailer\PHPMailer;

class SMTP {
    public PHPMailer $mail;

    public function __construct(){
     
        $this->mail = new PHPMailer(true);

        //$this->mail->SMTPDebug  = 1; 
        $this->mail->isSMTP();

        $this->mail->Host = GetEnv::requiredString('SMTP_HOST');
        $this->mail->SMTPAuth = GetEnv::bool('SMTP_AUTH', true) ?? true;
        $this->mail->Username = GetEnv::requiredString('SMTP_USERNAME');
        $this->mail->Password = GetEnv::requiredString('SMTP_PASSWORD');
        $this->mail->SMTPSecure = GetEnv::requiredString('SMTP_SECURE');
        $this->mail->Port = GetEnv::int('SMTP_PORT', 587) ?? 587;


    }

    public function getMailer(): PHPMailer {
        return $this->mail;
    }
}
