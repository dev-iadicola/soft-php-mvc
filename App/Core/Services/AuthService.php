<?php

namespace App\Core\Services;

use App\Model\User;
use App\Model\LogTraceTrace;
use App\Core\Eloquent\Model;
use App\Core\Eloquent\QueryBuilder;

class AuthService
{

    private SessionStorage $_sessionStorage;
    private ?Model $_user = null;
    public function __construct(SessionStorage $sessionStorage)
    {
        $this->_sessionStorage = $sessionStorage;
    }
    public function login(Model $model)
    {
       
        if ($model) {
            
            $token = static::generateToken();
     
           $model->token = $token;
           dump([$model->token , $token]);
           $model->save();
            static::startUserSession(token: $token);

            // salvataggio nel TraceLog
            
            // salvo la prop. user 
            $this->_user = $model;
            return true;
        }
        return false;
    }
    /**
     * Summary of logout
     * 
     * Elimina la sessione dell'utente
     * @return void
     */
    public function logout()
    {
        $this->_sessionStorage->destroy();
    }

    public function user(): ?QueryBuilder
    {
        return $this->_user;
    }


    /**
     * Summary of checkIpAddressSessionAndRemoteAddr
     * Verifica se l'utente abbia la sessione con l'IP con il quale si sia registrato e abbia lo stesso durante l'attività server
     * @return bool
     */
    public function checkIpAddressSessionAndRemoteAddr(): bool
    {
        return $this->_sessionStorage->get('IP') === $_SERVER['REMOTE_ADDR'];
    }

    public function isLogged(): bool
    {
        $logged = $this->_sessionStorage->get("LOGGED_IN") || $this->_user != null;
        return !empty($logged) || $logged == true;
    }

    public function isAuthenticated(): bool
    {
        if ($this->_sessionStorage->get('AUTH_TOKEN')) {
          
            $token = $this->_sessionStorage->get('AUTH_TOKEN');
            return $this->verifyTokenInDatabase($token);
        }
        return false;
    }

    public function updateLastPing(int $value): void
    {
        $this->_sessionStorage->setOrCreate('LAST_PING', $value);
    }

    public function destroySession()
    {
        $this->_sessionStorage->destroy();
    }

    protected function verifyTokenInDatabase($token)
    {
        $user = User::where('token', $token)->first();
        return (empty($user)) ? false : true;
    }





    private function startUserSession($token): void
    {
        $this->_sessionStorage->create([
            'AUTH_TOKEN' => $token,
            'LAST_PING' => time(),
            'LOGGED_IN' => TRUE,
            'IP' => $_SERVER['REMOTE_ADDR'],
            'DEVICE' =>  $_SERVER['HTTP_USER_AGENT']
        ]);        
    }


    protected static function generateToken($length = 32): string
    {
        return bin2hex(random_bytes($length));
    }


}
