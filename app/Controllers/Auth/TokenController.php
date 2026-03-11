<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Core\Controllers\Controller;
use App\Core\Helpers\Log;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Post;
use App\Core\Validation\Validator;
use App\Mail\BrevoMail;
use App\Model\User;
use App\Core\Http\Request;
use App\Model\Token;
use App\Services\PasswordService;
use App\Services\TokenService;

class TokenController extends Controller
{

    /**
     * Forgot password
     * request of token via mail for reset password
     */
    #[Post('/forgot', 'email.send')]
    public function forgotPasswordToken(Request $request): mixed
    {
        // Input fields validation
        $validator = Validator::make(
            $request->all(),
            ['email' => ['required', 'email']],
            ['email' => 'Invalid email format!']
        );

        if ($validator->fails() === true) {
            response()->withError($validator->errors());
            return view('Auth.forgot');
        }


        // Email address verification
        $user = User::query()->where('email', $request->string('email'))->first();
        if (! $user instanceof User) {
            return response()->back()->withError("Whoops, something went wrong!");
        }
        // Generation of token
        $token = TokenService::generate($request->string('email'));
        $to = $request->string('email');
        $subject = 'Richiesta di reset Password';
        $page = 'token-mail';

        // BrevoMail API send the mail with key
        $brevoMail = new BrevoMail();
        $brevoMail->bodyHtml($page, ['token' => $token]);
        $brevoMail->setEmail($to, $subject);
        $sent = $brevoMail->send();
        if (! $sent) {
            return response()->back()->withError("The mail wasn't sent. Verify your Brevo Account");
        }
        Log::info("Richiesta token per cambiare password accettata");
        return response()->back()->withSuccess('Mail was sent!, visit you email.');
    }



    /**
     * Validate pin page
     */
    #[Get('/validate-pin/{token}')]
    public function pagePin(Request $request, string $token): mixed
    {
        if (!TokenService::isValid($token)) {
            return view('Auth.forgot', ['message' => 'Non hai le credenziali per accedere']);
        }
        return view('Auth.validate-token', compact('token'));
    }

    #[Post('/token/change-password')]
    public function changePassword(Request $request): mixed
    {
        $data = $request->all();

        // Password validation
        $validatorPassword = Validator::make(
            $request->all(),
            ['password' => ["min:8", "confirmed"]],
            ['password' => "Password don't match"]
        );

        // If password validation fails, redirect back with error
        if ($validatorPassword->fails()) {
            return response()->back()->withError($validatorPassword->errors());
        }

        // Token validation
        $token = Token::query()->where('token', $request->string('token'))->first();
        if (! $token instanceof Token) {
            Log::Alert("Accesso sospetto: token mancante per la richiesta " . $request->uri() . "\n" . $request->getRequestInfo());
            return response()->set413();
        }

        $tokenEmail = $token->getAttribute('email');

        $changed = PasswordService::changeByEmail(email: (string) $tokenEmail, newPassword: $data['password']);

        // Invalidate the token after use
        Token::query()->where('token', $request->string('token'))->update(['used' => true]);

        if ($changed) {
            Log::email(
                "Password was changed for user {$tokenEmail}",
                (string) $tokenEmail,
                "Password Changed Successfully!"
            );
        }

        return response()->redirect("/login")->withSuccess('Accedi con le nuove credenziali!');
    }
}
