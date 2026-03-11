<?php

declare(strict_types=1);

namespace App\Controllers\Auth;


use App\Model\User;
use App\Core\Facade\Auth;
use App\Core\Http\Request;
use App\Core\Validation\Validator;
use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Post;
use App\Services\LogService;

class AuthController extends Controller
{

    #[Get('/login')]
    public function index(): void
    {
        // pagina login
        view('Auth.login');
    }

    #[Post('login', 'login')]
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
        // Autenticazione e traccia del log
        Auth::login($user);
        LogService::create((int) $user->getAttribute('id'), $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '');
        return redirect('admin/dashboard');
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
