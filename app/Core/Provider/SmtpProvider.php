<?php

declare(strict_types=1);

namespace App\Core\Provider;

use App\Core\GetEnv;
use App\Core\Connection\SMTP;
use App\Core\Helpers\Log;
use RuntimeException;
use PHPMailer\PHPMailer\Exception as ExceptionSMTP;


class SmtpProvider
{
    public function register(): ?SMTP
    {
        // SMTP is optional: if the host is not configured, return null silently.
        if (GetEnv::string('SMTP_HOST') === null || GetEnv::string('SMTP_HOST') === '') {
            return null;
        }

        try {
            return new SMTP();
        } catch (RuntimeException $e) {
            // SMTP_HOST is present but some variable is invalid (e.g. SMTP_PORT=abc).
            // Log a warning so the developer knows the config is broken, then re-throw.
            Log::error('SMTP configuration is invalid: ' . $e->getMessage());
            throw $e;
        } catch (ExceptionSMTP $e) {
            throw new RuntimeException('PHPMailer failed to initialise: ' . $e->getMessage(), 0, $e);
        }
    }
}
