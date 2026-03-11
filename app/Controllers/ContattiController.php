<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Model\User;
use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;
use App\Core\Exception\ValidationException;
use App\Mail\BrevoMail;
use App\Services\ContactService;

class ContattiController extends Controller
{


    #[RouteAttr('contatti', 'get', 'contatti')]
    public function index(): void
    {
        view('contatti');
    }


    #[RouteAttr('contatti', 'POST', 'contatti')]
    public function sendForm(Request $request)
    {
        $post = $request->all();

        try {
            ContactService::validate($post);
            ContactService::create($post);
        } catch (ValidationException) {
            return response()->back()->withError('Messaggio non inviato. Correggi i campi.');
        }

        // Notifica per via mail
        $user = User::query()->orderBy('id', 'desc')->first();
        $brevoMail = new BrevoMail();

        $brevoMail->bodyHtml('notifica', [
            'nome' => $request->string('nome'),
            'email' => $request->string('email'),
            'messaggio' => $request->string('messaggio'),
            'typologie' => $request->string('typologie'),
        ]);
        $brevoMail->setEmail($user->email, 'Messaggio dal tuo portfolio');
        $brevoMail->send();

        return response()->back()->withSuccess('Messaggio inviato con successo. Ti risponderò al più presto!');
    }
}
