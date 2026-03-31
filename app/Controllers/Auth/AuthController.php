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
use App\Core\Exception\ValidationException;
use App\Services\FirstUserSetupService;
use App\Services\LogService;
use App\Services\TotpService;

class AuthController extends Controller
{

    #[Get('/login')]
    public function index()
    {
        if (FirstUserSetupService::requiresRegistration()) {
            return response()
                ->redirect('/sign-up')
                ->withWarning('Nessun account trovato. Crea il primo account admin per iniziare.');
        }

        inertia('Auth/Login', [
            'meta' => [
                'title' => 'Login',
            ],
            'authPage' => [
                'title' => 'Login admin',
                'description' => 'Accedi al pannello per gestire contenuti, sicurezza e dashboard del portfolio.',
                'links' => [
                    ['href' => '/forgot', 'label' => 'Forgot password?'],
                    ['href' => '/sign-up', 'label' => 'Sei il primo? Registrati'],
                ],
            ],
        ]);
    }

    #[Post('login', 'login', 'rate_limit')]
    public function login(Request $request)
    {
        if (FirstUserSetupService::requiresRegistration()) {
            return response()
                ->redirect('/sign-up')
                ->withWarning('Nessun account trovato. Registrati per creare il primo accesso admin.');
        }

        // verifica esistenza user
        $user = User::query()->where('email', $request->get('email'))->first();

        if (! $user instanceof User) {
            return redirect()->back()->withError('Credenziali non valide.');
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

        return inertia('Auth/TwoFactor', [
            'meta' => [
                'title' => 'Verifica 2FA',
            ],
            'authPage' => [
                'title' => 'Verifica 2FA',
                'description' => 'Inserisci il codice TOTP generato dalla tua app di autenticazione per completare il login.',
            ],
        ]);
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
        if (FirstUserSetupService::requiresRegistration()) {
            return inertia('Auth/SignUp', [
                'meta' => [
                    'title' => 'Sign up iniziale',
                ],
                'authPage' => [
                    'title' => 'Crea il primo account admin',
                    'description' => 'Questo passaggio è disponibile solo quando il progetto non ha ancora alcun utente registrato.',
                ],
            ]);
        }

        return response()
            ->redirect('/login')
            ->withError('Registrazione chiusa: esiste gia un account admin.');
                
    }

    #[Post('/sign-up')]
    public function registration(Request $request)
    {
        try {
            FirstUserSetupService::ensureRegistrationOpen();
        } catch (ValidationException $e) {
            return response()
                ->redirect('/login')
                ->withError($e->getErrors());
        }

        $email = trim($request->string('email'));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->redirect()->back()->withError('Inserisci un indirizzo email valido.');
        }

        $confirmed =  Validator::make($request->all(),["password" => ["confirmed","required","min:8"]]);
        if ($confirmed->fails()) {
            return response()->redirect()->back()->withError($confirmed->errors()); 
        }

        try {
            FirstUserSetupService::createInitialUser(
                $email,
                password_hash($request->string('password'), PASSWORD_BCRYPT)
            );
        } catch (ValidationException $e) {
            return response()
                ->redirect('/login')
                ->withError($e->getErrors());
        }

        return response()->redirect('/login')->withSuccess('Account admin creato. Ora puoi effettuare il login.');
    }
    #[Post('/logout', 'logout')]
    public function logout(){
        mvc()->view->setLayout('default');
        Auth::logout();
        return response()->redirect('/login');
    }


}
