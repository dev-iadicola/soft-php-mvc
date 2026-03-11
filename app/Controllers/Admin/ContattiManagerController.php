<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Controllers\AdminController;
use App\Core\Exception\NotFoundException;
use App\Core\Http\Attributes\Delete;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Prefix;
use App\Services\ContactService;

#[Prefix('/admin')]
#[Middleware('auth')]
class ContattiManagerController extends AdminController
{

  #[Get('/contatti')]
  public function index()
  {
    $contatti = ContactService::getAll();
    return view('admin.portfolio.messaggi', compact('contatti'));
  }

  #[Get('contatti/{id}', 'admin.contatti')]
  public function get(Request $request, int $id)
  {
    $contatti = ContactService::getAll();
    $contatto = ContactService::findOrFail($id);
    return view('admin.portfolio.messaggi', compact('contatti', 'contatto'));
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
