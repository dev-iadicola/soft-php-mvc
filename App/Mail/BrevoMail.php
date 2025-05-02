<?php 
namespace App\Mail;

use App\Core\Contract\MailBaseInterface;
use GuzzleHttp\Client;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;

class BrevoMail extends BaseMail implements MailBaseInterface {
    private Configuration $config; 
    public function __construct(){
       $this->config =  Configuration::getDefaultConfiguration()->setApiKey('api-key',getenv('REVO_API_KEY'));
        new TransactionalEmailsApi(new Client(), $this->config );
    }

  public function setPage(string $page, $content = []): string{
    return mvc()->config;
  }

  public function sendEmail(string $to, string $subject, string $body, string|null $from = NULL, string|null $fromName = NULL){

  }
   /*  $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', 'LA_TUA_API_KEY');

$apiInstance = new SendinBlue\Client\Api\TransactionalEmailsApi(
    new GuzzleHttp\Client(),
    $config
);

$email = new \SendinBlue\Client\Model\SendSmtpEmail([
    'subject' => 'Reset password',
    'sender' => ['name' => 'Il Tuo Sito', 'email' => 'tuo@email.com'],
    'to' => [['email' => $userEmail]],
    'htmlContent' => '<p>Clicca qui per resettare la tua password</p>',
]);

$apiInstance->sendTransacEmail($email);

} */

}