<?php 
namespace App\Core\Services;

class CsrfService {
    private ?SessionStorage $_sessionStorage;

    public string $_key = 'csrf_token';
    public function __construct()
    {
        $this->_sessionStorage = SessionStorage::getInstance();
    }

     public function generateToken(): string
    {   
        if($this->_sessionStorage->)
        $token = bin2hex(random_bytes(32));
        $this->_sessionStorage->setOrCreate($this->_key, $token);
        $this->_sessionStorage->setTimeout(mvc()->config->settings["session"]["lifetime"]);
        return $token;
    }
    public function getToken(){
        return $this->_sessionStorage->get($this->_key);
    }

}