<?php

namespace App\Controllers\Auth;

use App\Core\Mailer\Mailer;
use App\Core\Mvc;
use App\Mail\BrevoMail;
use App\Model\User;
use App\Core\Validator;
use App\Core\Controller;
use App\Core\Http\Attributes\AttributeRoute;
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


    /**
     * Undocumented function
     *
     * @return void
     */
    #[AttributeRoute('/forgot','POST')]
    public function forgotPasswordToken(Request $request)
    {


        // Validazione dei campi input 

        $validator = Validator::validate(
            [$request->email  => 'email'],
            ['email' => 'Formato email Non valido!']
        );
        if ($validator->fails() === true) {
            $this->withError($validator->errors());
            return $this->render('Auth.forgot');
        }


        // Validazione Presenza Utente nel DB
        $user = User::where('email', $request->email)->first();
        if (empty($user)) {
            $this->withError('Errore, utente non presente');

            return $this->render('Auth.forgot');
        }

        // Creazione Token

        $token = Token::generateToken($request->email);
        $to = $request->email;
        $subject = 'Richiesta di reset Password';
        $page = 'token-mail';

        $brevoMail = new BrevoMail();
        $brevoMail->bodyHtml($page, ['token'=>$token]);
        $brevoMail->setEmail($to, $subject);
        $sended = $brevoMail->send();
        if (! $sended) {
            $this->withError('Errore, la mail non è stata inviata');
            return $this->render('Auth.forgot', ['message' => 'ERRORE: La mail non è stata inviata']);
        }
        // Reindirizzamento in caso di successo 

        $this->withSuccess('Mail inviata!');
        return $this->render('Auth.forgot', ['message' => 'Mail inviata con successo! Apri il link']);
    }

   

    /**
     * Summary of pagePin
     * @param \App\Core\Http\Request $request
     * @param mixed $token
     */
    #[AttributeRoute('/validate-pin/{token}')]
    public function pagePin(Request $request, $token)
    {
        if (Token::isBad($token)) {
            return $this->render('Auth.forgot', ['message' => 'Non hai le credenziali per accedere']);
        }
        return $this->render('Auth.validate-token', compact('token'));
    }

    public function validatePin(Request $request)
    {
        $data = $request->getPost();
       


        // Validazione della password
        $validatorPassword = Validator::confirmedPassword($data);

        if (!$validatorPassword) {
            $this->withError('Le password devono corrispondere');
            $this->redirectBack();
        }

        //Validazione del token
        $token =  Token::where('token', $data['token'])->first();

        if (empty($token)) {
            return  view('Auth.forgot', ['message' => 'La richiesta non è stata accettata!']);
        }
        $user = User::changePassword(password: $data['password'], email: $token->email);
        if (!empty($user)) {
            $brevoMail = new BrevoMail();
            $body = 'password-changed';
            $brevoMail->bodyHtml($body,['user'=>$user]);
            $to = $user->email;
            $subject = 'Password Cambiata con Successo';
            $brevoMail->setEmail($to, $subject);
            $brevoMail->send();

        }

        $this->withSuccess('Accedi con le nuove credenziali!');
        return view('Auth.login', ['message' => 'Accedi con le nuove credenziali']);
    }
}
