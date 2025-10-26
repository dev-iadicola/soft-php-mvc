<?php

namespace App\Core\Services;

use App\Model\Log;
use App\Model\User;

class AuthService
{
    public static function login($email)
    {
        $user = User::where('email', $email)->first();
        
        if (!empty($user)) {
            $token = static::generateToken();
            static::saveTokenToDatabase($user->id, $token);
            static::startUserSession(token: $token);
            
            self::saveLog($user->id);           
        }
        return false;
    }


    public static function logout()
    {
      SessionService::destroy();
    }


    
    protected static function startUserSession($token)
    {
        $sessionUser =  [
            'AUTH_TOKEN' => $token,
            'LAST_PING' => time(),
            'LOGGED_IN' => TRUE,
            'IP' => $_SERVER['REMOTE_ADDR'],
            'DEVICE' =>  $_SERVER['HTTP_USER_AGENT']
        ];

        SessionService::create($sessionUser);
    }

    
    protected static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }

    protected static function saveTokenToDatabase($userId, $token)
    {
        $user = User::where('id', $userId)->first();
        $user->update(['token' => $token]);
    }
    protected static function saveLog(int $userId){
        $log = Log::ceateLog($userId);
         if(empty($log)){
            return false;
         }
           return true;
    }
}
