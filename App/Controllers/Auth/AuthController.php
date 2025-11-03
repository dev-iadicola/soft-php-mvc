<?php

namespace App\Controllers\Auth;


use App\Model\User;
use App\Model\LogTrace;
use App\Core\Facade\Auth;
use App\Core\Http\Request;

use App\Core\Validation\Validator;
use App\Core\Controllers\BaseController;
use App\Core\Http\Attributes\AttributeRoute;


class AuthController extends BaseController
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
        // Autenticazione e traccia del log
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
        $confirmed =  Validator::make($request->all(),["password" => ["confirmed","required","min:8"]]);
        if ($confirmed->fails()) {
            return response()->redirect()->back()->withError($confirmed->errors()); 
        }
        $data['password'] = password_hash($request->password, PASSWORD_BCRYPT);
        User::upload($data);

        response()->redirect("/login")->withSuccess("Sign in comoplete, now sign up!");
    }
}
