<?php 
namespace App\Core\Contract; 

interface MailBaseInterface {


    // insert here the email address for send mail
    public function send(?string $var = null):object;

    public function directoryPage(string $page): void;

}