<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Controllers\AdminController;
use App\Core\Exception\NotFoundException;
use App\Services\ContactService;

class ContattiManagerController extends AdminController
{

  #[RouteAttr('/contatti')]
  public function index()
  {
    $contatti = ContactService::getAll();
    return view('admin.portfolio.messaggi', compact('contatti'));
  }

  #[RouteAttr('contatti/{id}','GET', 'admin.contatti')]
  public function get(Request $request, int $id)
  {
    $contatti = ContactService::getAll();
    $contatto = ContactService::findOrFail($id);
    return view('admin.portfolio.messaggi', compact('contatti', 'contatto'));
  }

  #[RouteAttr('/contatti-delete/{id}/','DELETE', 'admin.contatti.delete')]
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
