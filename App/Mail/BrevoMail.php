<?php

namespace App\Mail;

use GuzzleHttp\Client;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Api\EmailCampaignsApi;
use SendinBlue\Client\Model\CreateSmtpEmail;
use SendinBlue\Client\Model\CreateEmailCampaign;
use SendinBlue\Client\Api\TransactionalEmailsApi;

class BrevoMail extends BaseMail
{
    private Configuration $config;

    public $bodyHtml;
    private Client $httpClient;
    private TransactionalEmailsApi $transactionalEmailsApi;
    private SendSmtpEmail $mail;


    public function __construct()
    {
        parent::__construct();
        $this->config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', getenv('BREVO_API_KEY'));
        $this->httpClient = new Client();
        $this->transactionalEmailsApi = new TransactionalEmailsApi($this->httpClient, $this->config);
    }

    public function setEmail(string $to, string $subject, array $content = [], string|null $from = null, string|null $fromName = null)
    {
        $this->mail = new SendSmtpEmail([
        'to' => [['email' => $to]],
        'subject' => $subject,
        'sender' => [
            'name' => $fromName ?? $this->fromName,
            'email' => $from ?? $this->from,
        ],
        'htmlContent' => $this->bodyHtml,
    ]);
    
    }

    public function send(?string $email = null): CreateSmtpEmail
    {
        return $this->transactionalEmailsApi->sendTransacEmail($email ?? $this->mail);
    }
}
