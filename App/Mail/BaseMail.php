<?php 
namespace App\Mail; 

use App\Core\Contract\MailBaseInterface;

class BaseMail  {

    public ?string $from; 

    public ?string $formName; 

    public function __construct(){
        $this->form = getenv('APP_EMAIL') ?? null;
        $this->formName = getenv('APP_NAME') ?? null;
    }
    
    

    
}