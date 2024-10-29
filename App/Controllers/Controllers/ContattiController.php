<?php

namespace App\Controllers;

use \App\Core\Mvc;
use App\Model\User;
use App\Model\Contatti;
use \App\Core\Controller;
use App\Core\Http\Request;

class ContattiController extends Controller
{

    public function __construct(public Mvc $mvc)
    {
        parent::__construct($mvc);
    }

    public function index()
    {

        $this->render('contatti');
    }



    public function sendForm(Request $request)
    {
        if ($this->checkThsiForm()) {
            $this->withSuccess('Messaggio inviato con successo!');
            // Notifica per via mail
           /* $user = User::orderBy('id desc')->first();
            $mailer = $this->mvc->mailer;
            $to = $user->email;
            $subject = 'Hanno inviato un nuovo messaggio per il tuo portfolio!';
            $body = 'notifica';
            $mailer->setContent($request->getPost());
            $mailer->sendEmail($to, $subject, $body);
            */

        } else {
            $this->withError('Controlla i campi inseriti');
        }


        return $this->render('contatti');
    }

    public function checkThsiForm()
    {
        $model = new Contatti($this->mvc->pdo);
        $post = $this->mvc->request->getPost();
        if ($model->checkForm($post)) {
            $model->save($post);
            return true;
        }
        return false;
    }
}
