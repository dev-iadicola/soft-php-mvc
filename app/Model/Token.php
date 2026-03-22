<?php

declare(strict_types=1);

namespace App\Model;

use PDO;
use App\Core\DataLayer\Model;
use DateTime;

 class Token extends Model{


     public string $primaryKey = 'token';

     protected ?string $email = null;
     protected ?string $token = null;
     protected bool $used = false;
     protected ?string $created_at = null;
     protected ?string $expiry_date = null;
     protected ?string $updated_at = null;

     protected function casts(): array
     {
        return ['used' => 'bool'];
     }

     /**
      * @deprecated Use \App\Services\TokenService::generate() instead.
      */
     public static function generateToken(string $email): mixed{
        $token = bin2hex(random_bytes(100));
        $dataForToken = ['email' => $email, 'token' => $token];

        Token::query()->create($dataForToken);

        return Token::query()->where('token', $token)->first();
     }

     /**
      * Summary of verifyToken
      * @param mixed $token
      * @return bool
      *
      * Valida se il token ha i requisiti per permettere di accedere
      *
      * @deprecated Use \App\Services\TokenService::isValid() instead (inverted logic).
      */
     public static function isBad(string $token): bool{

        $tokenModel = Token::query()->where('token',$token)->first();

        if($tokenModel->used){
            return true;
        }
          $expiryDate = new DateTime($tokenModel->expiry_date);

        
        $currentDate = new DateTime();
        

        if ( $currentDate > $expiryDate) {
            return true;
        }

        return false;
     }
    
 }
