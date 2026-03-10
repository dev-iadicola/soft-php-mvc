<?php
namespace App\Model;

use PDO;
use App\Core\DataLayer\Model;
use DateTime;

 class Token extends Model{


    /**
     * Summary of table
     * @var string $table 
     * Questa variabile è importante per poter inserire staticamente il nome della colonna 
     * permettendoci di rispamiare tempo
     * 
     */
     protected string $table = 'tokens';
     public string $primaryKey = 'token';

     protected ?string $email = null;
     protected ?string $token = null;
     protected ?bool $used = null;
     protected ?string $created_at = null;
     protected ?string $expiry_date = null;

     protected function casts(): array
     {
        return ['used' => 'bool'];
     }

     public static function generateToken(string $email){
        $token = bin2hex(random_bytes(100));
        $dataForToken = ['email' => $email, 'token' => $token];

        Token::query()->create($dataForToken);

        return Token::query()->where('token', $token)->first();
     }

     /**
      * Summary of verifyToken
      * @param mixed $token
      * @return bool

      Valida se il token ha i requisiti per permettere di accedere 

      */
     public static function isBad($token){

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
