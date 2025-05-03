<?php

namespace App\Mail;

use App\Core\Contract\MailBaseInterface;

class BaseMail 
{

    public ?string $from;

    public ?string $formName;

    private ?string $page;

    public function __construct()
    {
        $this->from = getenv('APP_EMAIL') ?? null;
        $this->formName = getenv('APP_NAME') ?? null;
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


    public function getContent( $content = '')
    {
        ob_start(); // start capturing output
        if (is_object($content)) {
            $content = get_object_vars($content);
        }

        if (!is_array($content)) {
            throw new \InvalidArgumentException('Content must be an array or an object');
        }

        extract($content); // extract variables from content array

        include($this->page); // execute the file
        $output = ob_get_contents(); // get the contents from the buffer
        ob_end_clean(); // stop buffering and discard contents

        return $output; // return the captured content
    }
}
