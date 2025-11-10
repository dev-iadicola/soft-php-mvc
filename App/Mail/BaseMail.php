<?php

namespace App\Mail;

use App\Core\Contract\MailBaseInterface;

abstract class BaseMail implements MailBaseInterface
{

    public ?string $from;

    public ?string $fromName;
    public  $bodyHtml;
    private ?string $page;
    public function __construct()
    {
        $this->from = getenv('APP_EMAIL') ?? null;
        $this->fromName = getenv('APP_NAME') ?? null;
    }

    public function directoryPage($page): void
    {   
        
        $page = convertDotToSlash($page);
        $this->page = mvc()->config->folder->mails . '/' . $page . '.php';
        
    }

    public function bodyHtml(string $page, $content = '')
    {   
        
        $this->directoryPage($page);
        $this->getContent($content);
    }

    public function setEmail(string $to, string $subject, array $content = [], string|null $from = null, string|null $fromName = null){}


    public function getContent($data = []): void
    {
        ob_start();
        // Rende ogni chiave di $data disponibile come variabile isolata nella view
        foreach ($data as $key => $value) {
            $$key = $value; // crea variabile dinamica (es. $token = ...)
        }

        include($this->page);

        $this->bodyHtml = ob_get_clean();
    }
}
