<?php 
namespace App\Core\Contract; 

interface MailBaseInterface {


    public function send():object;

    public function directoryPage(string $page): void;

}