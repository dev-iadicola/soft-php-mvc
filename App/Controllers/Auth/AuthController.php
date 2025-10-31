<?php

namespace App\Controllers\Auth;

use App\Core\Http\Attributes\AttributeMiddleware;
use App\Core\Mvc;
use App\Model\User;
use App\Core\Validator;
use App\Model\LogTrace;
use App\Core\Controller;
use App\Core\Facade\Auth;
use App\Core\Http\Attributes\AttributeRoute;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Model\LogTraceTrace;
use App\Core\Services\AuthService;

class AuthController
{

    #[AttributeRoute('/login')]
    public function index()
    {
        // pagina login
        view('Auth.login');
    }

    #[AttributeRoute('login','POST', middleware:'auth')]
    public function login(Request $request)
    {
    
        // verifica esistenza user
        $user = User::where('email', $request->email)->first();

        if(empty($user)){
            return view('Auth.login', ['message' => 'Credenziali non valide!']);
        }

        // conferma password
        $confirmPassword = password_verify($request->password, $user->password);
        if ($confirmPassword === false) {
            return view('Auth.login', ['message' => 'Credenziali non valide!']);
        }
    
        Auth::login($user);
        LogTrace::ceateLog($user->id);
        return redirect('admin/dashboard');
    }

    #[AttributeRoute('forgot')]
    public function forgotPassword()
    {
        return view('Auth.forgot');
    }

    #[AttributeRoute('sign-up')]
    public function signUp()
    {
        $user = User::findAll();
        if (count($user) == 0) {
            return view('Auth.sign-up');
        } 
            // se esiste un utente, ritorna alla pagina di login
            return redirect('/');
                
    }

    #[AttributeRoute('/sign-up','post')]
    public function registration(Request $request)
    {
        $confirmed =  Validator::confirmedPassword($request->all());
        if ($confirmed === false) {
            return view('Auth.sign-up', ['message' => 'Le password non corrispondono']);
        }
        $data['password'] = password_hash($request->password, PASSWORD_BCRYPT);
        User::upload($data);
        view('Auth.login', ['message' => 'Registrazione Effettuata, Ora Iscriviti!']);
    }
}
