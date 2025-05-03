<?php

namespace App\Mail;

use GuzzleHttp\Client;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Api\EmailCampaignsApi;
use SendinBlue\Client\Api\TransactionalEmailsApi;

class BrevoMail extends BaseMail
{
  private Configuration $config;

  private SendSmtpEmail $email;

  private transactionalEmailsApi $transactionalEmailsApi;
  private EmailCampaignsApi $apiIstance;

  public function __construct()
  {
    parent::__construct();
    $this->config =  Configuration::getDefaultConfiguration()->setApiKey('api-key', getenv('REVO_API_KEY'));
    $this->transactionalEmailsApi = new TransactionalEmailsApi(new Client(), $this->config);
    $this->apiIstance = new EmailCampaignsApi();

  }




  public function setEmail(string $to, string $subject, string|null $page = null, array $content = [], string|null $from = NULL, string|null $fromName = NULL)
  {
    return new SendSmtpEmail([
      'subject' => $subject,
      'sender' => ['name' => $from ?? $this->formName, 'email' => $fromName ?? $this->from],
      'to' => [['email' => $to]],
      'htmlContent' => $body ?? $this->bodyHtml($page, $content),
    ]);
  }

  public function send()
  {
   return  $this->apiIstance->createEmailCampaign($this->email);
  }
}
