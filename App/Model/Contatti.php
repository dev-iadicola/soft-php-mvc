<?php

namespace App\Model;


use App\Core\ORM;

class Contatti extends ORM
{
    static string $table = 'contatti';
    protected static array $fillable = ['nome', 'email', 'messaggio', 'created_at','typologie'];


    public function checkForm($post)
    {
        $nome =  $post['nome'];
        $messaggio = $post['messaggio'];
        
        if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {

            return false;
        }
      
        if (strlen($nome) < 2 ||
            strlen($nome ) >= 100 ||
            strlen($messaggio) < 5) {
               
            return false;
        }

        return true;
    }
}
