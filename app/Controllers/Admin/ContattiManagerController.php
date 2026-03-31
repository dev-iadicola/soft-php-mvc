<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Exception\NotFoundException;
use App\Core\Http\Attributes\Delete;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Post;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Request;
use App\Mail\BrevoMail;
use App\Services\ContactService;

#[Prefix('/admin')]
#[Middleware('auth')]
class ContattiManagerController extends AdminController
{

  #[Get('/contatti')]
  public function index(Request $request)
  {
    $typologie = $request->get('typologie');
    $contatti = ($typologie !== null && $typologie !== '')
        ? ContactService::getByTypologie($typologie)
        : ContactService::getAll();
    $typologies = ContactService::getDistinctTypologies();

    return view('admin.portfolio.messaggi', compact('contatti', 'typologies', 'typologie'));
  }

  #[Get('contatti/{id}', 'admin.contatti')]
  public function get(int $id, Request $request)
  {
    ContactService::markAsRead($id);
    $typologie = $request->get('typologie');
    $contatti = ($typologie !== null && $typologie !== '')
        ? ContactService::getByTypologie($typologie)
        : ContactService::getAll();
    $contatto = ContactService::findOrFail($id);
    $typologies = ContactService::getDistinctTypologies();

    return view('admin.portfolio.messaggi', compact('contatti', 'contatto', 'typologies', 'typologie'));
  }

  #[Post('/contatti/{id}/read', 'admin.contatti.read')]
  public function markAsRead(int $id)
  {
    ContactService::markAsRead($id);
    return response()->back()->withSuccess('Messaggio segnato come letto.');
  }

  #[Post('/contatti/{id}/toggle-read', 'admin.contatti.toggleRead')]
  public function toggleRead(int $id)
  {
    $isRead = ContactService::toggleRead($id);
    $label = $isRead ? 'letto' : 'non letto';
    return response()->back()->withSuccess("Messaggio segnato come {$label}.");
  }

  #[Post('/contatti/{id}/reply', 'admin.contatti.reply')]
  public function reply(Request $request, int $id)
  {
    $contatto = ContactService::findOrFail($id);
    $body = trim((string) $request->get('reply_body'));

    if ($body === '') {
        return response()->back()->withError('Il testo della risposta non può essere vuoto.');
    }

    try {
        $mailer = new BrevoMail();
        $mailer->bodyHtml = $body;
        $mailer->setEmail(
            $contatto->email,
            'Re: Messaggio da portfolio',
        );
        $mailer->send();
    } catch (\Throwable $e) {
        return response()->back()->withError('Errore nell\'invio della risposta: ' . $e->getMessage());
    }

    return response()->back()->withSuccess("Risposta inviata a {$contatto->email}.");
  }

  #[Delete('/contatti-delete/{id}/', 'admin.contatti.delete')]
  public function destroy(int $id)
  {
    try {
        $contatto = ContactService::findOrFail($id);
        $info = "Nome: " . $contatto->nome . " Email:" . $contatto->email;
        ContactService::delete($id);
        return response()->back()->withSuccess("Messaggio eliminato: [$info]");
    } catch (NotFoundException) {
        return response()->back()->withError("Impossibile eliminare il messaggio: non trovato.");
    }
  }
}
