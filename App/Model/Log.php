<?php

namespace App\Model;


use App\Core\Eloquent\Model;
use App\Traits\Getter;
use App\Traits\Relation;

class Log extends Model
{
    use Getter; use Relation;
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

       $log = Log::save($default);

        return $log;
        
    }
}
