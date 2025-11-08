<?php 
namespace App\Core\Services;

class CsrfService {
    private ?SessionStorage $_sessionStorage;

    public string $_key = 'csrf_token';
    public function __construct()
    {
        $this->_sessionStorage = mvc()->sessionStorage;
    }

     public function generateToken(): void
    {   $token = bin2hex(random_bytes(32));
        $this->_sessionStorage->getOrCreate($this->_key,$token );   
    }
    public function getToken(){
        return $this->_sessionStorage->get($this->_key);
    }

}