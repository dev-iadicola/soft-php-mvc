<?php
namespace App\Model;

use App\Traits\Getter;
use PDO;
use App\Core\Eloquent\Model;
use DateTime;

 class Token extends Model{
   use Getter;

    /**
     * Summary of table
     * @var string $table 
     * Questa variabile è importante per poter inserire staticamente il nome della colonna 
     * permettendoci di rispamiare tempo
     * 
     */
     protected string $table = 'tokens'; 

     protected array $fillable = [
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