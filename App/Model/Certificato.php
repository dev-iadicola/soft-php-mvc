<?php

namespace App\Model;


use App\Core\ORM;

class Certificato extends ORM
{
    static string $table = 'corsi';

    static array $fillable = ['id', 'title', 'overview','certified','link','ente'];

   
}
