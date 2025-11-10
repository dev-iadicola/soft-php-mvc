<?php

namespace App\Controllers\Auth;

use App\Controllers\Admin\AbstractAdminController;
use App\Core\Controllers\Controller;
use App\Core\Helpers\Log;
use App\Core\Validation\Validator;
use App\Mail\BrevoMail;
use App\Model\User;
use App\Core\Http\Attributes\AttributeRoute;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;
use App\Model\Token;

class TokenController extends Controller
{

    /**
     * Forgot password
     * request of token via mail for reset password
     * @return void
     */
    #[RouteAttr('/forgot', 'POST', 'email.send')]
    public function forgotPasswordToken(Request $request)
    {
        // * Input fields validation
        $validator = Validator::make(
            $request->all(),
            ['email' => ['required', 'email']],
            ['email' => 'Invalid email format!']
        );

        if ($validator->fails() === true) {

            $this->withError($validator->errors());
            return $this->render('Auth.forgot');
        }


        // * email adress verificatrioin
        $user = User::where('email', $request->email)->first();
        if (empty($user)) {
            return response()->back()->withError("Whoops,something went worng!");
        }
        // * Generation of token
        $token = Token::generateToken($request->email);
        $to = $request->email;
        $subject = 'Richiesta di reset Password';
        $page = 'token-mail';

        // * BrevoMail API send the mail with key
        $brevoMail = new BrevoMail();
        $brevoMail->bodyHtml($page, ['token' => $token]);
        $brevoMail->setEmail($to, $subject);
        $sended = $brevoMail->send();
        if (! $sended) {
            return response()->back()->withError("The mail wasn't sent. Verify your Brevo Account");
        }
        Log::info("Richiesta token per cambiare passowrd accettata");
        return response()->back()->withSuccess('Mail was sent!, visit you email.');
    }



    /**
     * Summary of pagePin
     * @param \App\Core\Http\Request $request
     * @param mixed $token
     */
    #[RouteAttr('/validate-pin/{token}')]
    public function pagePin(Request $request, string $token)
    {
        if (Token::isBad($token)) {
            return $this->render('Auth.forgot', ['message' => 'Non hai le credenziali per accedere']);
        }
        return $this->render('Auth.validate-token', compact('token'));
    }

    #[RouteAttr("/token/change-password", "POST")]
    public function cahngePassword(Request $request)
    {
        $data = $request->all();

        // Validazione della password
        $validatorPassword = Validator::make($request->all(), [
            'password' => ["min:8", "confimed"],
            ["password" => "Password don't match"]
        ]);

        // If password dont match, redirect back with error message for the fileds worng
        if ($validatorPassword->fails()) {
            return response()->back()->withError($validatorPassword->errors());
        }

        //Validazione del token
        $token =  Token::where('token', $request->token)->first();
        if (empty($token) || is_null($token)) {
            // TODO: create a sistem to block 
            Log::Alert("Accesso sospetto: token mancante per la richiesta " . $request->uri() . "\n" . $request->getRequestInfo());
            return response()->set413();
        }
        $user = User::changePassword(password: $data['password'], email: $token->email);
        // * send email with email changed notofy
        if (!empty($user) || !is_null($user)) {

            Log::email("Passowrd was changed for user {$user->email}", $user->email, "Password Changed Successfully!");
        }

        return response()->redirect("/login")->withSuccess('Accedi con le nuove credenziali!');
    }
}
