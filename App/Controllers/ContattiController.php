<?php

namespace App\Controllers;

use \App\Core\Mvc;
use App\Model\User;
use App\Model\Contatti;
use App\Core\Controllers\BaseController;
use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;
use App\Mail\BrevoMail;

class ContattiController extends Controller
{

    public function __construct(public Mvc $mvc)
    {
        parent::__construct($mvc);
    }
    #[RouteAttr('contatti')]
    public function index()
    {

        return view('contatti');
    }


    #[RouteAttr('contatti', 'POST')]
    public function sendForm(Request $request)
    {
        if ($this->checkThsiForm()) {
            $this->withSuccess('Messaggio inviato con successo!');
            // Notifica per via mail
            $user = User::orderBy('id', 'desc')->first();
            $brevoMail = new BrevoMail();
            $page = 'notifica';

            $brevoMail->bodyHtml($page, [
                'nome' => $this->mvc->request->nome,
                'email' => $this->mvc->request->email,
                'messaggio' => $this->mvc->request->messaggio,
                'typologie' => $this->mvc->request->typologie,
            ]);
            $brevoMail->setEmail($user->email, 'Messaggio dal tuo portfolio');
            $sended = $brevoMail->send();
            $to = $user->email;

            $body = 'notifica';
        } else {
            $this->withError('Controlla i campi inseriti');
        }


        return view('contatti');
    }

    public function checkThsiForm()
    {
        $contatti = new Contatti();
        $post = $this->mvc->request->all();
        if ($contatti->checkForm($post)) {
            Contatti::create($post);
            return true;
        }
        return false;
    }
}
