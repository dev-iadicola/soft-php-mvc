<?php

namespace App\Model;


use App\Core\DataLayer\Model;


class LogTrace extends Model
{
    protected string $table = 'logs';

    protected array $fillable = ['user_id', 'last_log', 'indirizzo','device'];

    public static function ceateLog(int $id)
    {
        $default = [
            'user_id'=> $id, 
            'indirizzo' => $_SERVER['REMOTE_ADDR'], 
            'last_log' => date('Y-m-d H:i:s', time()),
            'device' =>  $_SERVER['HTTP_USER_AGENT']
        ];

       $log = LogTrace::create($default);

        return $log;
        
    }
}
