<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Model\User;
use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Post;
use App\Core\Http\Request;
use App\Core\Exception\ValidationException;
use App\Mail\BrevoMail;
use App\Services\ContactService;
use App\Services\ContactCardService;
use App\Services\ContactHeroService;
use App\Services\TechnologyService;

class ContattiController extends Controller
{


    #[Get('contatti', 'contatti')]
    public function index(): void
    {
        view('contatti', [
            'contactHero' => ContactHeroService::getLatest(),
            'contactCards' => ContactCardService::getAll(),
            'technologies' => TechnologyService::getAll(),
        ]);
    }


    #[Post('contatti', 'contatti')]
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
