<?php
namespace App\Model;

use PDO;
use App\Core\ORM;
use DateTime;

 class Token extends ORM{



    /**
     * Summary of table
     * @var string $table 
     * Questa variabile è importante per poter inserire staticamente il nome della colonna 
     * permettendoci di rispamiare tempo
     * 
     */
     static string $table = 'tokens'; 

     static array $fillable = [
        'email',
        'token',
        'used'
     ];

     public static function generateToken(string $email){
        $token = bin2hex(random_bytes(100));
        $dataForToken = ['email' => $email, 'token' => $token];
        
        parent::save($dataForToken);

        return token::where('token', $token)->first();
     }

     /**
      * Summary of verifyToken
      * @param mixed $token
      * @return bool

      Valida se il token ha i requisiti per permettere di accedere 

      */
     public static function isBad($token){

        $tokenModel = Token::where('token',$token)->first();

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