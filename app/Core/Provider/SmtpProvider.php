<?php

declare(strict_types=1);

namespace App\Core\Provider;

use App\Core\Http\Response;
use App\Core\Connection\SMTP;
use PHPMailer\PHPMailer\Exception as ExceptionSMTP;


class SmtpProvider
{
    public function __construct(private Response $response) {}
    public function register(): SMTP
    {
        try {
            return new SMTP();
        } catch (ExceptionSMTP $e) {
            response()->set550()->send();
            exit;
        }
    }
}
