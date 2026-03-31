<?php

declare(strict_types=1);

namespace App\Controllers\Auth;


use App\Model\User;
use App\Core\Facade\Auth;
use App\Core\Facade\Session;
use App\Core\Http\Request;
use App\Core\Validation\Validator;
use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Post;
use App\Services\LogService;
use App\Services\TotpService;

class AuthController extends Controller
{

    #[Get('/login')]
    public function index(): void
    {
        // pagina login
        view('Auth.login');
    }

    #[Post('login', 'login', 'rate_limit')]
    public function login(Request $request)
    {
        // verifica esistenza user
        $user = User::query()->where('email', $request->get('email'))->first();

        if (! $user instanceof User) {
            
            return view('Auth.login', ['message' => 'Credenziali non valide!']);
        }

        // conferma password
        $confirmPassword = password_verify($request->string('password'), (string) $user->getAttribute('password'));
        if ($confirmPassword === false) {
            return redirect()->back()->withError('Utente non presente.');
        }

        if ((bool) $user->two_factor_enabled && !empty($user->two_factor_secret)) {
            Session::create([
                'TWO_FACTOR_PENDING_USER_ID' => (int) $user->id,
                'TWO_FACTOR_PENDING_AT' => time(),
            ]);

            return redirect('/two-factor');
        }

        // Autenticazione e traccia del log
        Auth::login($user);
        LogService::create((int) $user->getAttribute('id'), $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '');
        return redirect('admin/dashboard');
    }

    #[Get('/two-factor')]
    public function twoFactorForm()
    {
        if ((int) Session::get('TWO_FACTOR_PENDING_USER_ID', 0) <= 0) {
            return redirect('/login');
        }

        return view('Auth.two-factor');
    }

    #[Post('/two-factor', 'auth.two-factor.verify')]
    public function verifyTwoFactor(Request $request)
    {
        $pendingUserId = (int) Session::get('TWO_FACTOR_PENDING_USER_ID', 0);

        if ($pendingUserId <= 0) {
            return response()->redirect('/login')->withError('Sessione 2FA scaduta. Effettua di nuovo il login.');
        }

        /** @var User|null $user */
        $user = User::query()->find($pendingUserId);

        if (!$user instanceof User || !$user->two_factor_enabled || empty($user->two_factor_secret)) {
            Session::removeMany(['TWO_FACTOR_PENDING_USER_ID', 'TWO_FACTOR_PENDING_AT']);
            return response()->redirect('/login')->withError('Autenticazione a due fattori non disponibile per questo account.');
        }

        if (!TotpService::verify((string) $user->two_factor_secret, $request->string('code'))) {
            return response()->back()->withError('Codice di verifica non valido.');
        }

        Session::removeMany(['TWO_FACTOR_PENDING_USER_ID', 'TWO_FACTOR_PENDING_AT']);

        Auth::login($user);
        LogService::create((int) $user->id, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '');

        return response()->redirect('/admin/dashboard');
    }

    #[Get('forgot')]
    public function forgotPassword()
    {
        return view('Auth.forgot');
    }

    #[Get('sign-up')]
    public function signUp()
    {
        $user = User::query()->all();
        if (count($user) == 0) {
            return view('Auth.sign-up');
        } 
            // se esiste un utente, ritorna alla pagina di login
            return redirect('/');
                
    }

    #[Post('/sign-up')]
    public function registration(Request $request)
    {
        $confirmed =  Validator::make($request->all(),["password" => ["confirmed","required","min:8"]]);
        if ($confirmed->fails()) {
            return response()->redirect()->back()->withError($confirmed->errors()); 
        }
        $data['password'] = password_hash($request->string('password'), PASSWORD_BCRYPT);
        User::upload($data);

        response()->redirect("/login")->withSuccess("Sign in comoplete, now sign up!");
    }
    #[Post('/logout', 'logout')]
    public function logout(){
        mvc()->view->setLayout('default');
        Auth::logout();
        return response()->redirect('/login');
    }


}
