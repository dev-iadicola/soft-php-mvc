<?php

declare(strict_types=1);

namespace App\Model;


use App\Core\DataLayer\Model;


class Contatti extends Model
{
    protected string $table = 'contatti';
    protected bool $timestamps = false;
    protected ?string $nome = null;
    protected ?string $email = null;
    protected ?string $messaggio = null;
    protected ?string $created_at = null;
    protected ?string $typologie = null;


    public function checkForm(array $post): bool
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
