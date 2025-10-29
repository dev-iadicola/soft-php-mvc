<?php

namespace App\Controllers\Auth;

use App\Core\Mvc;
use App\Model\User;
use App\Core\Validator;
use App\Model\LogTrace;
use App\Core\Controller;
use App\Core\Facade\Auth;
use App\Core\Http\Response;
use App\Model\LogTraceTrace;
use App\Core\Services\AuthService;

class AuthController extends Controller
{

    protected array|string|int|float $post;

    private AuthService $_authService;

    public function __construct(public Mvc $mvc)
    {
        parent::__construct($mvc);
        $this->post =  $this->mvc->request->getPost();
    }

    public function index()
    {
        // pagina login
        $this->render('Auth.login');
    }

    public function login()
    {
        // login post (quando inserisce le credenziali)
        $data = $this->post;
        // verifica esistenza user
        $user = User::where('email', $data['email'])->first();

        if(empty($user)){
            return view('Auth.login', ['message' => 'Credenziali non valide!']);
        }

        // conferma password
        $confirmPassword = password_verify($data['password'], $user->password);

        if ($confirmPassword === false) {
            return view('Auth.login', ['message' => 'Credenziali non valide!']);
        }
    
        Auth::login($user);
        LogTrace::ceateLog($user->id);
        return $this->mvc->response->redirect('admin/dashboard');
    }

    public function forgotPassword()
    {
        return view('Auth.forgot');
    }

    public function signUp()
    {
        $user = User::findAll();
        if (count($user) == 0) {
            return view('Auth.sign-up');
        } else {
            // se esiste un utente, ritorna alla pagina di login
            $this->mvc->response->redirect('/login');
        }
    }

    public function registration()
    {
        $data = $this->post;


        $confirmed =  Validator::confirmedPassword($data);
        if ($confirmed === false) {
            return view('Auth.sign-up', ['message' => 'Le password non corrispondono']);
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        User::upload($data);

        $this->render('Auth.login', ['message' => 'Registrazione Effettuata, Ora Iscriviti!']);
    }
}
