<?php

namespace App\Mail;

use App\Core\Contract\MailBaseInterface;

class BaseMail 
{

    public ?string $from;

    public ?string $fromName;
    public string $bodyHtml;
    private ?string $page;
    public function __construct()
    {
        $this->from = getenv('APP_EMAIL') ?? null;
        $this->fromName = getenv('APP_NAME') ?? null;
    }

    public function mailPage($dir): void
    {
        $page = convertDotToSlash($dir);
        $this->page = mvc()->config->folder->mails . '/' . $page . '.php';
    }

    public function bodyHtml(string $page, $content = ''): bool|string{
        $this->mailPage($page);
        return $this->getContent($content);
    }


    public function getContent($content = '')
    {
        ob_start();
    
        if (is_object($content)) {
            $content = get_object_vars($content);
        }
    
        if (!is_array($content)) {
            throw new \InvalidArgumentException('Content must be an array or an object');
        }
    
        extract($content, EXTR_SKIP); // evita di sovrascrivere variabili esistenti
    
        include($this->page);
    
        return $this->bodyHtml = ob_get_clean();
    }
    
}
