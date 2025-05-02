<?php

namespace App\Controllers\Auth;

use App\Core\Mailer\Mailer;
use App\Core\Mvc;
use App\Model\User;
use App\Core\Validator;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Model\Token;

class TokenController extends Controller
{

    public array $post;


    public function __construct(public Mvc $mvc)
    {
        parent::__construct($mvc);
        parent::setLayout('default');
        $this->post =  $this->mvc->request->getPost();
    }

   

    public function forgotPasswordToken()
    {
        $post = $this->post;
       

        // Validazione dei campi input 

        $validator = Validator::validate(
            [$post['email']  => 'email'],
            ['email' => 'Formato email Non valido!']
        );
        if ($validator->fails() === true) {
            $this->withError($validator->errors());
            return $this->render('Auth.forgot');
        }


        // Validazione Presenza Utente nel DB
        $user = User::where('email', $post['email'])->first();
        if (empty($user)) {
            $this->withError('Errore, utente non presente');

            return $this->render('Auth.forgot');
        }
        
        // Creazione Token

       $tokenModel =  Token::generateToken($post['email']);
      
        $mailer = $this->mvc->mailer;
        $mailer->setContent($tokenModel);
        $to = $post['email'];
        $subject = 'Richiesta di reset Password';
        $body = 'token-mail' ;
        
       //attendere per l'algoritmo per poter prendere il file da inviare anzichè un HTML

        // Validazione Mail

        if (!$mailer->sendEmail($to, $subject, $body)) {
            $this->withError('Errore, la mail non è stata inviata');
            return $this->render('Auth.forgot', ['message' => 'ERRORE: La mail non è stata inviata']);
        }

        // Reindirizzamento di successo 

        $this->withSuccess('Mail inviata!');
        return $this->render('Auth.forgot', ['message' => 'Mail inviata con successo! Apri il link']);
    }

    /**
     * 
     * 
     * @return void
     * 
     * validazione token gestione richiesta POST e reindirizzamento per modifica password
     */

     public function pagePin(Request $request, $token){

        $message = '';
        
        if(Token::isBad($token)){
            return $this->render('Auth.forgot',['message'=>'Non hai le credenziali per accedere']);
        }
       
       return $this->render('Auth.validate-token', compact('token','message'));

     }

     public function validatePin(Request $request){
        $data = $request->getPost();


        // Validazione della password
        $validatorPassword = Validator::confirmedPassword( $data);

        if(!$validatorPassword){
            $this->withError('Le password devono corrispondere');
            $this->redirectBack();
        }



        //Validazione del token
       $token =  Token::where('token', $data['token'])->first();


       
       if(empty($token)){
       return  $this->render('Auth.forgot',['message'=> 'La richiesta non è stata accettata!']);
       }
      

       $user = User::changePassword(password: $data['password'], email: $token->email);
       if(!empty($user)){
        $mailer = $this->mvc->mailer;
        $mailer->setContent($user);
        $to = $user->email;
        $subject = 'Password Cambiata con Successo';
        $body = 'password-changed' ;
        
     

        $mailer->sendEmail($to, $subject, $body);
       }

       $this->withSuccess('Accedi con le nuove credenziali!');
       return $this->render('Auth.login',['message'=>'Accedi con le nuove credenziali']);
       
      
        
     }
 
}
